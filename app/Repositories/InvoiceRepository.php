<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use PDO;

class InvoiceRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT i.*, 
                   r.room_number,
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   c.contract_number
            FROM `invoices` i
            JOIN `tenants` t ON t.id = i.tenant_id
            JOIN `users` u ON u.id = t.user_id
            JOIN `rental_contracts` c ON c.id = i.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tenant_id'])) {
            $sql .= " AND i.tenant_id = :tenant_id";
            $params['tenant_id'] = (int)$filters['tenant_id'];
        }

        if (!empty($filters['contract_id'])) {
            $sql .= " AND i.contract_id = :contract_id";
            $params['contract_id'] = (int)$filters['contract_id'];
        }

        if (!empty($filters['due_from'])) {
            $sql .= " AND i.due_date >= :due_from";
            $params['due_from'] = $filters['due_from'];
        }

        if (!empty($filters['due_to'])) {
            $sql .= " AND i.due_date <= :due_to";
            $params['due_to'] = $filters['due_to'];
        }

        $sql .= " ORDER BY i.due_date DESC, i.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $invoices = [];
        foreach ($rows as $row) {
            $inv = new Invoice($row);
            $inv->room_number = $row['room_number'];
            $inv->tenant_name = $row['tenant_name'];
            $inv->tenant_phone = $row['tenant_phone'] ?? '';
            $inv->contract_number = $row['contract_number'];
            $invoices[] = $inv;
        }

        return $invoices;
    }

    public function findById(int $id): ?Invoice
    {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   r.room_number,
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   c.contract_number
            FROM `invoices` i
            JOIN `tenants` t ON t.id = i.tenant_id
            JOIN `users` u ON u.id = t.user_id
            JOIN `rental_contracts` c ON c.id = i.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            WHERE i.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $inv = new Invoice($row);
        $inv->room_number = $row['room_number'];
        $inv->tenant_name = $row['tenant_name'];
        $inv->tenant_phone = $row['tenant_phone'] ?? '';
        $inv->contract_number = $row['contract_number'];
        $inv->items = $this->getItemsByInvoiceId($id);

        return $inv;
    }

    public function findByInvoiceNumber(string $number): ?Invoice
    {
        $stmt = $this->db->prepare("SELECT id FROM `invoices` WHERE `invoice_number` = :num LIMIT 1");
        $stmt->execute(['num' => $number]);
        $id = $stmt->fetchColumn();

        return $id ? $this->findById((int)$id) : null;
    }

    public function getByTenantId(int $tenantId): array
    {
        return $this->getAll(['tenant_id' => $tenantId]);
    }

    public function getItemsByInvoiceId(int $invoiceId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `invoice_items` WHERE `invoice_id` = :id ORDER BY `id` ASC");
        $stmt->execute(['id' => $invoiceId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new InvoiceItem($r), $rows);
    }

    public function findDuplicate(int $contractId, string $periodStart, string $periodEnd, ?int $excludeId = null): ?Invoice
    {
        $sql = "
            SELECT id FROM `invoices` 
            WHERE `contract_id` = :contract_id 
              AND `period_start` = :start 
              AND `period_end` = :end
              AND `status` NOT IN ('cancelled', 'void')
        ";
        $params = [
            'contract_id' => $contractId,
            'start' => $periodStart,
            'end' => $periodEnd,
        ];

        if ($excludeId) {
            $sql .= " AND `id` != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $id = $stmt->fetchColumn();

        return $id ? $this->findById((int)$id) : null;
    }

    public function createInvoice(array $data, array $items): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `invoices` (
                `invoice_number`, `contract_id`, `tenant_id`, 
                `period_start`, `period_end`, `issue_date`, `due_date`, 
                `subtotal`, `total_amount`, `paid_amount`, `balance`, 
                `is_first_month_manual`, `status`, `notes`
            ) VALUES (
                :invoice_number, :contract_id, :tenant_id, 
                :period_start, :period_end, :issue_date, :due_date, 
                :subtotal, :total_amount, :paid_amount, :balance, 
                :is_first_month_manual, :status, :notes
            )
        ");

        $stmt->execute([
            'invoice_number' => $data['invoice_number'],
            'contract_id' => $data['contract_id'],
            'tenant_id' => $data['tenant_id'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $data['subtotal'],
            'total_amount' => $data['total_amount'],
            'paid_amount' => $data['paid_amount'] ?? 0.00,
            'balance' => $data['balance'],
            'is_first_month_manual' => $data['is_first_month_manual'] ?? 0,
            'status' => $data['status'] ?? 'unpaid',
            'notes' => $data['notes'] ?? null,
        ]);

        $invoiceId = (int)$this->db->lastInsertId();

        $stmtItem = $this->db->prepare("
            INSERT INTO `invoice_items` (`invoice_id`, `description`, `amount`)
            VALUES (?, ?, ?)
        ");

        foreach ($items as $item) {
            $stmtItem->execute([
                $invoiceId,
                $item['description'],
                (float)$item['amount'],
            ]);
        }

        return $invoiceId;
    }

    public function updateStatusAndBalance(int $invoiceId, string $status, float $paidAmount, float $balance): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `invoices`
            SET `status` = :status, `paid_amount` = :paid, `balance` = :balance, `updated_at` = NOW()
            WHERE `id` = :id
        ");

        return $stmt->execute([
            'status' => $status,
            'paid' => $paidAmount,
            'balance' => $balance,
            'id' => $invoiceId,
        ]);
    }

    public function updateOverdueInvoices(): int
    {
        $stmt = $this->db->prepare("
            UPDATE `invoices`
            SET `status` = 'overdue', `updated_at` = NOW()
            WHERE `due_date` < CURDATE()
              AND `status` IN ('unpaid', 'partially_paid')
        ");
        $stmt->execute();

        return $stmt->rowCount();
    }
}

