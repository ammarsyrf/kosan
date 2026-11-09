<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Payment;
use PDO;

class PaymentRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT p.*, 
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   pp.file_path AS proof_file
            FROM `payments` p
            JOIN `tenants` t ON t.id = p.tenant_id
            JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `payment_proofs` pp ON pp.payment_id = p.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tenant_id'])) {
            $sql .= " AND p.tenant_id = :tenant_id";
            $params['tenant_id'] = (int)$filters['tenant_id'];
        }

        if (!empty($filters['method'])) {
            $sql .= " AND p.payment_method = :method";
            $params['method'] = $filters['method'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND p.payment_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND p.payment_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY p.payment_date DESC, p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $payments = [];
        foreach ($rows as $row) {
            $payment = new Payment($row);
            $payment->tenant_name = $row['tenant_name'];
            $payment->tenant_phone = $row['tenant_phone'] ?? '';
            $payment->proof_file = $row['proof_file'] ?? null;
            $payment->allocations = $this->getAllocations($payment->id);
            $payments[] = $payment;
        }

        return $payments;
    }

    public function findById(int $id): ?Payment
    {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   pp.file_path AS proof_file
            FROM `payments` p
            JOIN `tenants` t ON t.id = p.tenant_id
            JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `payment_proofs` pp ON pp.payment_id = p.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $payment = new Payment($row);
        $payment->tenant_name = $row['tenant_name'];
        $payment->tenant_phone = $row['tenant_phone'] ?? '';
        $payment->proof_file = $row['proof_file'] ?? null;
        $payment->allocations = $this->getAllocations($id);

        return $payment;
    }

    public function getByTenantId(int $tenantId): array
    {
        return $this->getAll(['tenant_id' => $tenantId]);
    }

    public function createPayment(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `payments` (
                `payment_number`, `tenant_id`, `amount`, `payment_method`, 
                `payment_date`, `status`, `notes`
            ) VALUES (
                :payment_number, :tenant_id, :amount, :payment_method, 
                :payment_date, :status, :notes
            )
        ");

        $stmt->execute([
            'payment_number' => $data['payment_number'],
            'tenant_id' => $data['tenant_id'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_date' => $data['payment_date'],
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function addProof(int $paymentId, array $fileData): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `payment_proofs` (
                `payment_id`, `file_path`, `file_name`, `file_size`, `mime_type`
            ) VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $paymentId,
            $fileData['file_path'],
            $fileData['file_name'],
            $fileData['file_size'],
            $fileData['mime_type'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getProofByPaymentId(int $paymentId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `payment_proofs` WHERE `payment_id` = ? LIMIT 1");
        $stmt->execute([$paymentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function updateStatus(int $id, string $status, ?int $verifiedBy = null, ?string $reason = null): bool
    {
        $sql = "UPDATE `payments` SET `status` = :status, `updated_at` = NOW()";
        $params = ['id' => $id, 'status' => $status];

        if ($status === 'verified') {
            $sql .= ", `verified_by` = :by, `verified_at` = NOW()";
            $params['by'] = $verifiedBy;
        } elseif ($status === 'rejected') {
            $sql .= ", `rejection_reason` = :reason, `verified_by` = :by, `verified_at` = NOW()";
            $params['reason'] = $reason;
            $params['by'] = $verifiedBy;
        }

        $sql .= " WHERE `id` = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function addAllocation(int $paymentId, int $invoiceId, float $amount): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `payment_allocations` (`payment_id`, `invoice_id`, `allocated_amount`)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$paymentId, $invoiceId, $amount]);

        return (int)$this->db->lastInsertId();
    }

    public function getAllocations(int $paymentId): array
    {
        $stmt = $this->db->prepare("
            SELECT pa.*, i.invoice_number, i.total_amount, i.balance
            FROM `payment_allocations` pa
            JOIN `invoices` i ON i.id = pa.invoice_id
            WHERE pa.payment_id = ?
        ");
        $stmt->execute([$paymentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAdjustment(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `payment_adjustments` (
                `payment_id`, `adjustment_type`, `amount`, `reason`, `adjusted_by`
            ) VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['payment_id'],
            $data['adjustment_type'],
            $data['amount'],
            $data['reason'],
            $data['adjusted_by'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAdjustments(int $paymentId): array
    {
        $stmt = $this->db->prepare("
            SELECT paj.*, u.name AS adjuster_name
            FROM `payment_adjustments` paj
            JOIN `users` u ON u.id = paj.adjusted_by
            WHERE paj.payment_id = ?
            ORDER BY paj.adjusted_at DESC
        ");
        $stmt->execute([$paymentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

