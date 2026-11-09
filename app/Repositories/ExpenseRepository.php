<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use PDO;

class ExpenseRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    // --- Expense Categories ---
    public function getAllCategories(bool $activeOnly = false): array
    {
        $sql = "
            SELECT c.*,
                   COUNT(e.id) AS expenses_count,
                   COALESCE(SUM(e.amount), 0) AS expenses_total
            FROM `expense_categories` c
            LEFT JOIN `expenses` e ON e.category_id = c.id AND e.deleted_at IS NULL
        ";

        if ($activeOnly) {
            $sql .= " WHERE c.is_active = 1";
        }

        $sql .= " GROUP BY c.id ORDER BY c.name ASC";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new ExpenseCategory($r), $rows);
    }

    public function findCategoryById(int $id): ?ExpenseCategory
    {
        $stmt = $this->db->prepare("SELECT * FROM `expense_categories` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new ExpenseCategory($row) : null;
    }

    public function countExpensesByCategoryId(int $categoryId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM `expenses`
            WHERE `category_id` = :cid AND `deleted_at` IS NULL
        ");
        $stmt->execute(['cid' => $categoryId]);

        return (int)$stmt->fetchColumn();
    }

    public function createCategory(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `expense_categories` (`name`, `description`, `is_active`, `created_at`)
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['is_active'] ?? 1,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateCategory(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `expense_categories`
            SET `name` = :name, `description` = :desc, `is_active` = :active, `updated_at` = NOW()
            WHERE `id` = :id
        ");

        return $stmt->execute([
            'name' => $data['name'],
            'desc' => $data['description'] ?? null,
            'active' => $data['is_active'] ?? 1,
            'id' => $id,
        ]);
    }

    public function deleteCategory(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `expense_categories` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }

    // --- Expenses ---
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT e.*, c.name AS category_name, u.name AS creator_name
            FROM `expenses` e
            JOIN `expense_categories` c ON c.id = e.category_id
            JOIN `users` u ON u.id = e.created_by
            WHERE e.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= " AND e.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.transaction_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND e.transaction_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.description LIKE :q OR e.vendor LIKE :q2)";
            $params['q'] = '%' . $filters['search'] . '%';
            $params['q2'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY e.transaction_date DESC, e.id DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new Expense($r), $rows);
    }

    public function count(array $filters = []): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM `expenses` e
            WHERE e.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= " AND e.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.transaction_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND e.transaction_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.description LIKE :q OR e.vendor LIKE :q2)";
            $params['q'] = '%' . $filters['search'] . '%';
            $params['q2'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?Expense
    {
        $stmt = $this->db->prepare("
            SELECT e.*, c.name AS category_name, u.name AS creator_name
            FROM `expenses` e
            JOIN `expense_categories` c ON c.id = e.category_id
            JOIN `users` u ON u.id = e.created_by
            WHERE e.id = :id AND e.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Expense($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `expenses` (
                `category_id`, `transaction_date`, `amount`, `description`,
                `vendor`, `payment_method`, `receipt_path`, `created_by`, `notes`,
                `created_at`, `updated_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $data['category_id'],
            $data['transaction_date'],
            $data['amount'],
            $data['description'],
            $data['vendor'] ?? null,
            $data['payment_method'] ?? 'cash',
            $data['receipt_path'] ?? null,
            $data['created_by'],
            $data['notes'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `expenses`
            SET `category_id` = :cat_id,
                `transaction_date` = :tx_date,
                `amount` = :amount,
                `description` = :desc,
                `vendor` = :vendor,
                `payment_method` = :method,
                `receipt_path` = COALESCE(:receipt, `receipt_path`),
                `notes` = :notes,
                `updated_at` = NOW()
            WHERE `id` = :id AND `deleted_at` IS NULL
        ");

        return $stmt->execute([
            'cat_id' => $data['category_id'],
            'tx_date' => $data['transaction_date'],
            'amount' => $data['amount'],
            'desc' => $data['description'],
            'vendor' => $data['vendor'] ?? null,
            'method' => $data['payment_method'] ?? 'cash',
            'receipt' => $data['receipt_path'] ?? null,
            'notes' => $data['notes'] ?? null,
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        // Soft delete for financial audit trail
        $stmt = $this->db->prepare("UPDATE `expenses` SET `deleted_at` = NOW() WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function permanentDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `expenses` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }
}
