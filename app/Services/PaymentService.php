<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;
use App\Models\Payment;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use PDO;
use RuntimeException;

class PaymentService
{
    private PaymentRepository $paymentRepo;
    private InvoiceRepository $invoiceRepo;

    public function __construct(
        ?PaymentRepository $paymentRepo = null,
        ?InvoiceRepository $invoiceRepo = null
    ) {
        $this->paymentRepo = $paymentRepo ?? new PaymentRepository();
        $this->invoiceRepo = $invoiceRepo ?? new InvoiceRepository();
    }

    public function getAllPayments(array $filters = []): array
    {
        return $this->paymentRepo->getAll($filters);
    }

    public function getPaymentById(int $id): ?Payment
    {
        return $this->paymentRepo->findById($id);
    }

    public function getTenantPayments(int $tenantId): array
    {
        return $this->paymentRepo->getByTenantId($tenantId);
    }

    public function generatePaymentNumber(string $date): string
    {
        $yearMonth = date('Ym', strtotime($date));
        $prefix = "PAY/{$yearMonth}/";

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `payments` WHERE `payment_number` LIKE :prefix");
        $stmt->execute(['prefix' => "{$prefix}%"]);
        $count = (int)$stmt->fetchColumn() + 1;

        return sprintf("%s%04d", $prefix, $count);
    }

    public function submitTenantPayment(array $data, ?array $file = null, ?string $actor = 'Tenant'): int
    {
        return Database::transaction(function (PDO $pdo) use ($data, $file, $actor) {
            $tenantId = (int)$data['tenant_id'];
            $amount = (float)$data['amount'];
            $method = (string)($data['payment_method'] ?? 'transfer');
            $date = (string)($data['payment_date'] ?? date('Y-m-d'));

            if ($amount <= 0) {
                throw new RuntimeException("Nominal pembayaran harus lebih dari 0.");
            }

            if ($method === 'transfer' && (!$file || empty($file['tmp_name']))) {
                throw new RuntimeException("Pembayaran via bank transfer wajib menyertakan bukti transfer.");
            }

            $payNumber = $this->generatePaymentNumber($date);

            $paymentId = $this->paymentRepo->createPayment([
                'payment_number' => $payNumber,
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'payment_method' => $method,
                'payment_date' => $date,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Save uploaded proof if any
            if ($file && !empty($file['tmp_name'])) {
                $savedFile = $this->storeProofFile($paymentId, $file);
                $this->paymentRepo->addProof($paymentId, $savedFile);
            }

            // If an invoice is referenced, record intent (does not allocate yet because payment is pending)
            if (!empty($data['invoice_id'])) {
                $invId = (int)$data['invoice_id'];
                $invoice = $this->invoiceRepo->findById($invId);
                if ($invoice && $invoice->tenant_id === $tenantId) {
                    // Stored in notes for admin reference during verification
                    $stmt = $pdo->prepare("UPDATE `payments` SET `notes` = CONCAT(IFNULL(`notes`,''), ' [Target Invoice: #', :inv, ']') WHERE `id` = :id");
                    $stmt->execute(['inv' => $invoice->invoice_number, 'id' => $paymentId]);
                }
            }

            Logger::audit((string)$actor, 'SUBMIT_PAYMENT', 'payments', $paymentId, [
                'payment_number' => $payNumber,
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'method' => $method,
            ]);

            return $paymentId;
        });
    }

    public function recordCashPayment(array $data, ?int $adminId = null, ?string $actor = 'Admin'): int
    {
        return Database::transaction(function (PDO $pdo) use ($data, $adminId, $actor) {
            $tenantId = (int)$data['tenant_id'];
            $amount = (float)$data['amount'];
            $date = (string)($data['payment_date'] ?? date('Y-m-d'));

            if ($amount <= 0) {
                throw new RuntimeException("Nominal pembayaran tunai harus lebih dari 0.");
            }

            $payNumber = $this->generatePaymentNumber($date);

            $paymentId = $this->paymentRepo->createPayment([
                'payment_number' => $payNumber,
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'payment_method' => 'cash',
                'payment_date' => $date,
                'status' => 'verified',
                'notes' => $data['notes'] ?? 'Diterima tunai oleh admin',
            ]);

            $stmtVerif = $pdo->prepare("UPDATE `payments` SET `verified_by` = :by, `verified_at` = NOW() WHERE `id` = :id");
            $stmtVerif->execute(['by' => $adminId, 'id' => $paymentId]);

            // Auto-allocate if invoice_id is specified
            if (!empty($data['invoice_id'])) {
                $invoiceId = (int)$data['invoice_id'];
                $this->allocatePaymentDirectly($pdo, $paymentId, $invoiceId, $amount, $tenantId);
            }

            Logger::audit((string)$actor, 'RECORD_CASH_PAYMENT', 'payments', $paymentId, [
                'payment_number' => $payNumber,
                'amount' => $amount,
            ]);

            return $paymentId;
        });
    }

    public function verifyPayment(int $paymentId, array $allocations, int $adminId, ?string $actor = 'Admin'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($paymentId, $allocations, $adminId, $actor) {
            // Lock payment row
            $stmtLock = $pdo->prepare("SELECT * FROM `payments` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $paymentId]);
            $payment = $stmtLock->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                throw new RuntimeException("Data pembayaran tidak ditemukan.");
            }

            if ($payment['status'] !== 'pending') {
                throw new RuntimeException("Hanya pembayaran berstatus pending yang dapat diverifikasi.");
            }

            $totalAmount = (float)$payment['amount'];
            $allocatedSum = 0.00;

            if (empty($allocations)) {
                throw new RuntimeException("Tentukan alokasi pembayaran minimal ke 1 tagihan (invoice).");
            }

            foreach ($allocations as $alloc) {
                $invoiceId = (int)$alloc['invoice_id'];
                $amountToAlloc = (float)$alloc['amount'];

                if ($amountToAlloc <= 0) {
                    continue;
                }

                $allocatedSum += $amountToAlloc;

                $this->allocatePaymentDirectly($pdo, $paymentId, $invoiceId, $amountToAlloc, (int)$payment['tenant_id']);
            }

            if (round($allocatedSum, 2) > round($totalAmount, 2)) {
                throw new RuntimeException("Total alokasi (Rp " . number_format($allocatedSum, 0, ',', '.') . ") melebihi nominal pembayaran (Rp " . number_format($totalAmount, 0, ',', '.') . ").");
            }

            // Update payment status to verified
            $stmtUpdate = $pdo->prepare("
                UPDATE `payments`
                SET `status` = 'verified', `verified_by` = :by, `verified_at` = NOW(), `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmtUpdate->execute(['by' => $adminId, 'id' => $paymentId]);

            Logger::audit((string)$actor, 'VERIFY_PAYMENT', 'payments', $paymentId, [
                'verified_by' => $adminId,
                'allocated_sum' => $allocatedSum,
            ]);

            return true;
        });
    }

    public function rejectPayment(int $paymentId, string $reason, int $adminId, ?string $actor = 'Admin'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($paymentId, $reason, $adminId, $actor) {
            $stmtLock = $pdo->prepare("SELECT * FROM `payments` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $paymentId]);
            $payment = $stmtLock->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                throw new RuntimeException("Pembayaran tidak ditemukan.");
            }

            if ($payment['status'] !== 'pending') {
                throw new RuntimeException("Hanya pembayaran berstatus pending yang dapat ditolak.");
            }

            if (empty(trim($reason))) {
                throw new RuntimeException("Alasan penolakan pembayaran wajib diisi.");
            }

            $stmt = $pdo->prepare("
                UPDATE `payments`
                SET `status` = 'rejected', `rejection_reason` = :reason, `verified_by` = :by, `verified_at` = NOW(), `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmt->execute(['reason' => $reason, 'by' => $adminId, 'id' => $paymentId]);

            Logger::audit((string)$actor, 'REJECT_PAYMENT', 'payments', $paymentId, [
                'rejected_by' => $adminId,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    public function reversePayment(int $paymentId, string $reason, int $adminId, ?string $actor = 'Admin'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($paymentId, $reason, $adminId, $actor) {
            $stmtLock = $pdo->prepare("SELECT * FROM `payments` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $paymentId]);
            $payment = $stmtLock->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                throw new RuntimeException("Pembayaran tidak ditemukan.");
            }

            if ($payment['status'] !== 'verified') {
                throw new RuntimeException("Hanya pembayaran terverifikasi yang dapat dibalikkan/dikoreksi.");
            }

            if (empty(trim($reason))) {
                throw new RuntimeException("Alasan reversal/koreksi wajib diisi.");
            }

            // Get all allocations of this payment
            $stmtAlloc = $pdo->prepare("SELECT * FROM `payment_allocations` WHERE `payment_id` = :id FOR UPDATE");
            $stmtAlloc->execute(['id' => $paymentId]);
            $allocations = $stmtAlloc->fetchAll(PDO::FETCH_ASSOC);

            // Revert each invoice balance and status
            foreach ($allocations as $alloc) {
                $invoiceId = (int)$alloc['invoice_id'];
                $amount = (float)$alloc['allocated_amount'];

                $stmtInv = $pdo->prepare("SELECT * FROM `invoices` WHERE `id` = :id FOR UPDATE");
                $stmtInv->execute(['id' => $invoiceId]);
                $inv = $stmtInv->fetch(PDO::FETCH_ASSOC);

                if ($inv) {
                    $newPaid = max(0.00, (float)$inv['paid_amount'] - $amount);
                    $newBalance = (float)$inv['total_amount'] - $newPaid;
                    $newStatus = ($newPaid <= 0.00) ? 'unpaid' : 'partially_paid';

                    $stmtUpdateInv = $pdo->prepare("
                        UPDATE `invoices`
                        SET `paid_amount` = :paid, `balance` = :balance, `status` = :status, `updated_at` = NOW()
                        WHERE `id` = :id
                    ");
                    $stmtUpdateInv->execute([
                        'paid' => $newPaid,
                        'balance' => $newBalance,
                        'status' => $newStatus,
                        'id' => $invoiceId,
                    ]);
                }
            }

            // Delete allocations
            $stmtDelAlloc = $pdo->prepare("DELETE FROM `payment_allocations` WHERE `payment_id` = :id");
            $stmtDelAlloc->execute(['id' => $paymentId]);

            // Update payment status to reversed
            $stmtUpdatePay = $pdo->prepare("
                UPDATE `payments`
                SET `status` = 'reversed', `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmtUpdatePay->execute(['id' => $paymentId]);

            // Record adjustment entry
            $this->paymentRepo->addAdjustment([
                'payment_id' => $paymentId,
                'adjustment_type' => 'reversal',
                'amount' => (float)$payment['amount'],
                'reason' => $reason,
                'adjusted_by' => $adminId,
            ]);

            Logger::audit((string)$actor, 'REVERSE_PAYMENT', 'payments', $paymentId, [
                'reversed_by' => $adminId,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    private function allocatePaymentDirectly(PDO $pdo, int $paymentId, int $invoiceId, float $amount, int $tenantId): void
    {
        // Lock invoice row
        $stmtLock = $pdo->prepare("SELECT * FROM `invoices` WHERE `id` = :id FOR UPDATE");
        $stmtLock->execute(['id' => $invoiceId]);
        $invoice = $stmtLock->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new RuntimeException("Invoice ID #{$invoiceId} tidak ditemukan.");
        }

        if ((int)$invoice['tenant_id'] !== $tenantId) {
            throw new RuntimeException("Akses ditolak: Invoice #{$invoice['invoice_number']} bukan milik penyewa ini.");
        }

        if (in_array($invoice['status'], ['paid', 'cancelled', 'void'], true)) {
            throw new RuntimeException("Invoice #{$invoice['invoice_number']} sudah lunas atau dibatalkan.");
        }

        $currentBalance = (float)$invoice['balance'];
        if (round($amount, 2) > round($currentBalance, 2)) {
            throw new RuntimeException("Nominal alokasi (Rp " . number_format($amount, 0, ',', '.') . ") melebihi sisa tagihan invoice #{$invoice['invoice_number']} (Rp " . number_format($currentBalance, 0, ',', '.') . ").");
        }

        // Insert allocation
        $stmtAlloc = $pdo->prepare("
            INSERT INTO `payment_allocations` (`payment_id`, `invoice_id`, `allocated_amount`)
            VALUES (?, ?, ?)
        ");
        $stmtAlloc->execute([$paymentId, $invoiceId, $amount]);

        // Recalculate invoice balance
        $newPaid = (float)$invoice['paid_amount'] + $amount;
        $newBalance = max(0.00, (float)$invoice['total_amount'] - $newPaid);
        $newStatus = ($newBalance <= 0.00) ? 'paid' : 'partially_paid';

        $stmtUpdate = $pdo->prepare("
            UPDATE `invoices`
            SET `paid_amount` = :paid, `balance` = :balance, `status` = :status, `updated_at` = NOW()
            WHERE `id` = :id
        ");
        $stmtUpdate->execute([
            'paid' => $newPaid,
            'balance' => $newBalance,
            'status' => $newStatus,
            'id' => $invoiceId,
        ]);
    }

    private function storeProofFile(int $paymentId, array $file): array
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        if (PHP_VERSION_ID < 80500) {
            finfo_close($finfo);
        }

        if (!in_array($mime, $allowedMimes, true)) {
            throw new RuntimeException("Format file bukti transfer tidak valid. Hanya menerima JPG, PNG, WEBP, atau PDF.");
        }

        $origName = basename($file['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            throw new RuntimeException("Ekstensi file tidak diizinkan.");
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxSize) {
            throw new RuntimeException("Ukuran file bukti transfer maksimal 5 MB.");
        }

        $uploadDir = dirname(__DIR__, 2) . '/storage/uploads/payment_proofs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $randomName = 'proof_' . $paymentId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetPath = $uploadDir . '/' . $randomName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            // In CLI / testing, fallback to copy if move_uploaded_file fails
            if (!copy($file['tmp_name'], $targetPath)) {
                throw new RuntimeException("Gagal menyimpan file bukti transfer ke server.");
            }
        }

        return [
            'file_path' => 'payment_proofs/' . $randomName,
            'file_name' => $origName,
            'file_size' => (int)$file['size'],
            'mime_type' => $mime,
        ];
    }
}
