<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\MaintenanceAttachment;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceUpdate;
use PDO;

class MaintenanceRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    // --- Categories ---
    public function getAllCategories(): array
    {
        $stmt = $this->db->query("SELECT * FROM `maintenance_categories` ORDER BY `name` ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new MaintenanceCategory($r), $rows);
    }

    public function findCategoryById(int $id): ?MaintenanceCategory
    {
        $stmt = $this->db->prepare("SELECT * FROM `maintenance_categories` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new MaintenanceCategory($row) : null;
    }

    public function createCategory(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `maintenance_categories` (`name`, `description`, `created_at`)
            VALUES (?, ?, NOW())
        ");

        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateCategory(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `maintenance_categories`
            SET `name` = :name, `description` = :desc
            WHERE `id` = :id
        ");

        return $stmt->execute([
            'name' => $data['name'],
            'desc' => $data['description'] ?? null,
            'id' => $id,
        ]);
    }

    // --- Maintenance Requests ---
    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT m.*,
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   r.room_number,
                   c.name AS category_name
            FROM `maintenance_requests` m
            JOIN `tenants` t ON t.id = m.tenant_id
            JOIN `users` u ON u.id = t.user_id
            JOIN `rooms` r ON r.id = m.room_id
            JOIN `maintenance_categories` c ON c.id = m.category_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND m.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND m.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND m.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['room_id'])) {
            $sql .= " AND m.room_id = :room_id";
            $params['room_id'] = (int)$filters['room_id'];
        }

        if (!empty($filters['tenant_id'])) {
            $sql .= " AND m.tenant_id = :tenant_id";
            $params['tenant_id'] = (int)$filters['tenant_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(m.created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(m.created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY m.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $req = new MaintenanceRequest($row);
            $req->attachments = $this->getAttachmentsByRequestId($req->id);
            $results[] = $req;
        }

        return $results;
    }

    public function getByTenantId(int $tenantId): array
    {
        return $this->getAll(['tenant_id' => $tenantId]);
    }

    public function findById(int $id): ?MaintenanceRequest
    {
        $stmt = $this->db->prepare("
            SELECT m.*,
                   u.name AS tenant_name, u.phone AS tenant_phone,
                   r.room_number,
                   c.name AS category_name
            FROM `maintenance_requests` m
            JOIN `tenants` t ON t.id = m.tenant_id
            JOIN `users` u ON u.id = t.user_id
            JOIN `rooms` r ON r.id = m.room_id
            JOIN `maintenance_categories` c ON c.id = m.category_id
            WHERE m.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $req = new MaintenanceRequest($row);
        $req->attachments = $this->getAttachmentsByRequestId($req->id);

        return $req;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `maintenance_requests` (
                `tenant_id`, `room_id`, `category_id`, `title`,
                `description`, `priority`, `status`, `created_at`, `updated_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $data['tenant_id'],
            $data['room_id'],
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'submitted',
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $resolvedAt = null): bool
    {
        $sql = "UPDATE `maintenance_requests` SET `status` = :status, `updated_at` = NOW()";
        $params = ['status' => $status, 'id' => $id];

        if ($status === 'resolved') {
            $sql .= ", `resolved_at` = COALESCE(:resolved_at, NOW())";
            $params['resolved_at'] = $resolvedAt;
        }

        $sql .= " WHERE `id` = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePriority(int $id, string $priority): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `maintenance_requests`
            SET `priority` = :priority, `updated_at` = NOW()
            WHERE `id` = :id
        ");

        return $stmt->execute([
            'priority' => $priority,
            'id' => $id,
        ]);
    }

    // --- Attachments ---
    public function createAttachment(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `maintenance_attachments` (
                `request_id`, `file_path`, `file_name`, `mime_type`, `created_at`
            ) VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['request_id'],
            $data['file_path'],
            $data['file_name'],
            $data['mime_type'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAttachmentsByRequestId(int $requestId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `maintenance_attachments`
            WHERE `request_id` = :rid
            ORDER BY `id` ASC
        ");
        $stmt->execute(['rid' => $requestId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new MaintenanceAttachment($r), $rows);
    }

    public function findAttachmentById(int $id): ?MaintenanceAttachment
    {
        $stmt = $this->db->prepare("SELECT * FROM `maintenance_attachments` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new MaintenanceAttachment($row) : null;
    }

    // --- Updates & History ---
    public function createUpdate(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `maintenance_updates` (
                `request_id`, `user_id`, `status_changed_to`, `notes`, `is_internal`, `created_at`
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['request_id'],
            $data['user_id'],
            $data['status_changed_to'] ?? null,
            $data['notes'],
            $data['is_internal'] ?? 0,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getUpdatesByRequestId(int $requestId, bool $includeInternal = true): array
    {
        $sql = "
            SELECT u.*, us.name AS user_name, r.name AS user_role
            FROM `maintenance_updates` u
            JOIN `users` us ON us.id = u.user_id
            LEFT JOIN `role_user` ru ON ru.user_id = us.id
            LEFT JOIN `roles` r ON r.id = ru.role_id
            WHERE u.request_id = :rid
        ";

        if (!$includeInternal) {
            $sql .= " AND u.is_internal = 0";
        }

        $sql .= " ORDER BY u.created_at ASC, u.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rid' => $requestId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new MaintenanceUpdate($r), $rows);
    }
}
