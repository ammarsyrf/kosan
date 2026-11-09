<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\NotificationTemplate;
use App\Models\WhatsAppLog;
use PDO;

class NotificationRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    // --- Templates ---
    public function getAllTemplates(): array
    {
        $stmt = $this->db->query("SELECT * FROM `notification_templates` ORDER BY `code` ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new NotificationTemplate($r), $rows);
    }

    public function findTemplateByCode(string $code): ?NotificationTemplate
    {
        $stmt = $this->db->prepare("SELECT * FROM `notification_templates` WHERE `code` = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new NotificationTemplate($row) : null;
    }

    public function updateTemplate(int $id, string $content, int $isActive): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `notification_templates`
            SET `content` = :content, `is_active` = :is_active, `updated_at` = NOW()
            WHERE `id` = :id
        ");

        return $stmt->execute([
            'content' => $content,
            'is_active' => $isActive,
            'id' => $id,
        ]);
    }

    // --- WhatsApp Logs ---
    public function findLogByIdempotencyKey(string $key): ?WhatsAppLog
    {
        $stmt = $this->db->prepare("SELECT * FROM `whatsapp_logs` WHERE `idempotency_key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new WhatsAppLog($row) : null;
    }

    public function findLogById(int $id): ?WhatsAppLog
    {
        $stmt = $this->db->prepare("SELECT * FROM `whatsapp_logs` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new WhatsAppLog($row) : null;
    }

    public function getAllLogs(array $filters = []): array
    {
        $sql = "SELECT * FROM `whatsapp_logs` WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND `status` = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['phone'])) {
            $sql .= " AND `recipient_phone` LIKE :phone";
            $params['phone'] = "%" . $filters['phone'] . "%";
        }

        $sql .= " ORDER BY `created_at` DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new WhatsAppLog($r), $rows);
    }

    public function createLog(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `whatsapp_logs` (
                `idempotency_key`, `recipient_phone`, `message`, `status`, `response_payload`
            ) VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['idempotency_key'],
            $data['recipient_phone'],
            $data['message'],
            $data['status'] ?? 'pending',
            $data['response_payload'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateLogStatus(int $id, string $status, ?string $responsePayload = null): bool
    {
        $sql = "UPDATE `whatsapp_logs` SET `status` = :status, `response_payload` = :payload";
        if ($status === 'sent') {
            $sql .= ", `last_sent_at` = NOW()";
        }
        $sql .= " WHERE `id` = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'payload' => $responsePayload,
            'id' => $id,
        ]);
    }

    public function incrementRetryCount(int $id, string $status, ?string $responsePayload = null): bool
    {
        $sql = "UPDATE `whatsapp_logs` SET `status` = :status, `retry_count` = `retry_count` + 1, `response_payload` = :payload";
        if ($status === 'sent') {
            $sql .= ", `last_sent_at` = NOW()";
        }
        $sql .= " WHERE `id` = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'payload' => $responsePayload,
            'id' => $id,
        ]);
    }

    public function getPendingRetries(int $maxRetries = 3): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `whatsapp_logs`
            WHERE `status` IN ('failed', 'retrying')
              AND `retry_count` < :max
            ORDER BY `created_at` ASC
            LIMIT 50
        ");
        $stmt->execute(['max' => $maxRetries]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new WhatsAppLog($r), $rows);
    }

    // --- Notification & Schedule Settings ---
    public function getNotificationSettings(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM `settings` WHERE `group` = 'notification'");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function updateNotificationSetting(string $key, string $value): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO `settings` (`key`, `value`, `group`, `created_at`, `updated_at`)
            VALUES (:key, :val, 'notification', NOW(), NOW())
            ON DUPLICATE KEY UPDATE `value` = :val2, `updated_at` = NOW()
        ");

        return $stmt->execute([
            'key' => $key,
            'val' => $value,
            'val2' => $value,
        ]);
    }
}
