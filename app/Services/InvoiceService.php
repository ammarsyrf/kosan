<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;
use App\Models\Invoice;
use App\Models\RentalContract;
use App\Repositories\ContractRepository;
use App\Repositories\InvoiceRepository;
use PDO;
use RuntimeException;

class InvoiceService
{
    private InvoiceRepository $invoiceRepo;
    private ContractRepository $contractRepo;

    public function __construct(
        ?InvoiceRepository $invoiceRepo = null,
        ?ContractRepository $contractRepo = null
    ) {
        $this->invoiceRepo = $invoiceRepo ?? new InvoiceRepository();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
    }

    public function getAllInvoices(array $filters = []): array
    {
        return $this->invoiceRepo->getAll($filters);
    }

    public function getInvoiceById(int $id): ?Invoice
    {
        return $this->invoiceRepo->findById($id);
    }

    public function getTenantInvoices(int $tenantId): array
    {
        return $this->invoiceRepo->getByTenantId($tenantId);
    }

    public function generateInvoiceNumber(string $date): string
    {
        $yearMonth = date('Ym', strtotime($date));
        $prefix = "INV/{$yearMonth}/";

        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM `invoices` 
            WHERE `invoice_number` LIKE :prefix
        ");
        $stmt->execute(['prefix' => "{$prefix}%"]);
        $count = (int)$stmt->fetchColumn() + 1;

        return sprintf("%s%04d", $prefix, $count);
    }

    public function createManualInvoice(array $data, array $items, ?string $actor = 'Admin'): int
    {
        return Database::transaction(function (PDO $pdo) use ($data, $items, $actor) {
            $contractId = (int)$data['contract_id'];
            $contract = $this->contractRepo->findById($contractId);
            if (!$contract) {
                throw new RuntimeException("Kontrak sewa tidak ditemukan.");
            }

            if (empty($items)) {
                throw new RuntimeException("Rincian item tagihan (invoice items) tidak boleh kosong.");
            }

            $periodStart = (string)$data['period_start'];
            $periodEnd = (string)$data['period_end'];

            if ($periodStart > $periodEnd) {
                throw new RuntimeException("Tanggal awal periode tidak boleh melewati tanggal akhir periode.");
            }

            // Check duplicate invoice for same contract and period
            $existing = $this->invoiceRepo->findDuplicate($contractId, $periodStart, $periodEnd);
            if ($existing) {
                throw new RuntimeException("Invoice untuk kontrak #{$contract->contract_number} pada periode {$periodStart} s/d {$periodEnd} sudah pernah diterbitkan (Invoice #{$existing->invoice_number}).");
            }

            $issueDate = $data['issue_date'] ?? date('Y-m-d');
            $dueDate = $data['due_date'] ?? date('Y-m-d', strtotime($issueDate . ' + 7 days'));

            $subtotal = 0.00;
            foreach ($items as $item) {
                $subtotal += (float)$item['amount'];
            }

            $totalAmount = $subtotal;
            $balance = $totalAmount;

            $invNumber = $this->generateInvoiceNumber($issueDate);

            $invoiceId = $this->invoiceRepo->createInvoice([
                'invoice_number' => $invNumber,
                'contract_id' => $contractId,
                'tenant_id' => $contract->tenant_id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'balance' => $balance,
                'is_first_month_manual' => !empty($data['is_first_month_manual']) ? 1 : 0,
                'status' => 'unpaid',
                'notes' => $data['notes'] ?? null,
            ], $items);

            Logger::audit((string)$actor, 'CREATE_INVOICE', 'invoices', $invoiceId, [
                'invoice_number' => $invNumber,
                'contract_id' => $contractId,
                'total_amount' => $totalAmount,
                'is_first_month_manual' => !empty($data['is_first_month_manual']) ? 1 : 0,
            ]);

            return $invoiceId;
        });
    }

    public function generateRoutineInvoice(int $contractId, int $year, int $month, ?string $actor = 'System'): ?int
    {
        return Database::transaction(function (PDO $pdo) use ($contractId, $year, $month, $actor) {
            $contract = $this->contractRepo->findById($contractId);
            if (!$contract || !$contract->isActive()) {
                return null;
            }

            // Calculate period based on billing anchor day
            $anchorDay = $contract->billing_anchor_day;
            $periodStartDate = RentalContract::calculateDueDateForMonth($anchorDay, $year, $month);

            // Period end is 1 day before the next month's anchor date
            $nextMonth = $month == 12 ? 1 : $month + 1;
            $nextYear = $month == 12 ? $year + 1 : $year;
            $nextAnchorDate = RentalContract::calculateDueDateForMonth($anchorDay, $nextYear, $nextMonth);
            $periodEndDate = date('Y-m-d', strtotime($nextAnchorDate . ' - 1 day'));

            // Check if period is strictly within contract date range
            if ($periodStartDate > $contract->end_date || $periodEndDate < $contract->start_date) {
                return null;
            }

            // Check duplicate
            $existing = $this->invoiceRepo->findDuplicate($contractId, $periodStartDate, $periodEndDate);
            if ($existing) {
                return null; // Already generated
            }

            $issueDate = $periodStartDate;
            $dueDate = date('Y-m-d', strtotime($periodStartDate . ' + 7 days'));

            $items = [
                [
                    'description' => "Biaya Sewa Kamar {$contract->room_number} Periode " . date('d M Y', strtotime($periodStartDate)) . " s/d " . date('d M Y', strtotime($periodEndDate)),
                    'amount' => $contract->rent_price_snapshot,
                ]
            ];

            $invNumber = $this->generateInvoiceNumber($issueDate);

            $invoiceId = $this->invoiceRepo->createInvoice([
                'invoice_number' => $invNumber,
                'contract_id' => $contractId,
                'tenant_id' => $contract->tenant_id,
                'period_start' => $periodStartDate,
                'period_end' => $periodEndDate,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'subtotal' => $contract->rent_price_snapshot,
                'total_amount' => $contract->rent_price_snapshot,
                'paid_amount' => 0.00,
                'balance' => $contract->rent_price_snapshot,
                'is_first_month_manual' => 0,
                'status' => 'unpaid',
                'notes' => "Invoice rutin bulanan otomatis",
            ], $items);

            Logger::audit((string)$actor, 'GENERATE_ROUTINE_INVOICE', 'invoices', $invoiceId, [
                'contract_id' => $contractId,
                'period_start' => $periodStartDate,
                'period_end' => $periodEndDate,
            ]);

            return $invoiceId;
        });
    }

    public function cancelInvoice(int $invoiceId, string $reason, ?string $actor = 'Admin'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($invoiceId, $reason, $actor) {
            $invoice = $this->invoiceRepo->findById($invoiceId);
            if (!$invoice) {
                throw new RuntimeException("Invoice tidak ditemukan.");
            }

            if ($invoice->paid_amount > 0) {
                throw new RuntimeException("Invoice yang sudah memiliki pembayaran terverifikasi tidak dapat dibatalkan secara langsung.");
            }

            $stmt = $pdo->prepare("
                UPDATE `invoices`
                SET `status` = 'cancelled', `notes` = CONCAT(IFNULL(`notes`,''), ' | Dibatalkan: ', :reason), `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmt->execute(['id' => $invoiceId, 'reason' => $reason]);

            Logger::audit((string)$actor, 'CANCEL_INVOICE', 'invoices', $invoiceId, ['reason' => $reason]);
            return true;
        });
    }

    public function updateOverdueInvoices(): int
    {
        return $this->invoiceRepo->updateOverdueInvoices();
    }
}

