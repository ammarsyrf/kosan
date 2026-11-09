<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Tenant;
use PDO;

class TenantRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT t.*, u.name, u.email, u.phone, u.status AS account_status,
                   r.room_number
            FROM `tenants` t
            JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `rental_contracts` rc ON rc.tenant_id = t.id AND rc.status = 'active'
            LEFT JOIN `rooms` r ON r.id = rc.room_id
            ORDER BY u.name ASC
        ";

        $rows = $this->db->query($sql)->fetchAll();
        return array_map(function ($row) {
            $tenant = new Tenant($row);
            $tenant->name = $row['name'];
            $tenant->email = $row['email'];
            $tenant->phone = $row['phone'];
            $tenant->status = $row['account_status'];
            $tenant->room_number = $row['room_number'] ?? null;
            return $tenant;
        }, $rows);
    }

    public function findById(int $id): ?Tenant
    {
        $stmt = $this->db->prepare("
            SELECT t.*, u.name, u.email, u.phone, u.status AS account_status,
                   r.room_number
            FROM `tenants` t
            JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `rental_contracts` rc ON rc.tenant_id = t.id AND rc.status = 'active'
            LEFT JOIN `rooms` r ON r.id = rc.room_id
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $tenant = new Tenant($row);
        $tenant->name = $row['name'];
        $tenant->email = $row['email'];
        $tenant->phone = $row['phone'];
        $tenant->status = $row['account_status'];
        $tenant->room_number = $row['room_number'] ?? null;
        return $tenant;
    }

    public function findByUserId(int $userId): ?Tenant
    {
        $stmt = $this->db->prepare("SELECT * FROM `tenants` WHERE `user_id` = :uid LIMIT 1");
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? new Tenant($row) : null;
    }

    public function createTenant(array $userData, array $tenantData): array
    {
        return Database::transaction(function (PDO $pdo) use ($userData, $tenantData) {
            // 1. Create User account with temporary dummy password and status 'pending'
            $tempPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

            $stmtUser = $pdo->prepare("
                INSERT INTO `users` (`name`, `email`, `password`, `phone`, `status`)
                VALUES (:name, :email, :password, :phone, 'pending')
            ");
            $stmtUser->execute([
                'name' => trim($userData['name']),
                'email' => strtolower(trim($userData['email'])),
                'password' => $tempPassword,
                'phone' => trim($userData['phone'] ?? ''),
            ]);
            $userId = (int)$pdo->lastInsertId();

            // 2. Assign Tenant Role
            $roleId = $pdo->query("SELECT `id` FROM `roles` WHERE `name` = 'tenant'")->fetchColumn();
            $stmtRole = $pdo->prepare("INSERT INTO `role_user` (`user_id`, `role_id`) VALUES (?, ?)");
            $stmtRole->execute([$userId, $roleId]);

            // 3. Create Tenant Profile
            $stmtTenant = $pdo->prepare("
                INSERT INTO `tenants` (`user_id`, `id_card_number`, `emergency_contact_name`, `emergency_contact_phone`, `occupation`, `notes`)
                VALUES (:user_id, :id_card_number, :emergency_contact_name, :emergency_contact_phone, :occupation, :notes)
            ");
            $stmtTenant->execute([
                'user_id' => $userId,
                'id_card_number' => trim($tenantData['id_card_number'] ?? ''),
                'emergency_contact_name' => trim($tenantData['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => trim($tenantData['emergency_contact_phone'] ?? ''),
                'occupation' => trim($tenantData['occupation'] ?? ''),
                'notes' => trim($tenantData['notes'] ?? ''),
            ]);
            $tenantId = (int)$pdo->lastInsertId();

            // 4. Generate Activation Token (PRD requirement: single-use, hashed token with expiry)
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $expiresAt = date('Y-m-d H:i:s', time() + (48 * 3600)); // 48 hours

            $stmtToken = $pdo->prepare("
                INSERT INTO `account_activation_tokens` (`user_id`, `token_hash`, `expires_at`)
                VALUES (?, ?, ?)
            ");
            $stmtToken->execute([$userId, $tokenHash, $expiresAt]);

            return [
                'user_id' => $userId,
                'tenant_id' => $tenantId,
                'raw_token' => $rawToken,
                'expires_at' => $expiresAt,
            ];
        });
    }

    public function verifyActivationToken(string $rawToken): ?array
    {
        $tokenHash = hash('sha256', $rawToken);
        $stmt = $this->db->prepare("
            SELECT t.*, u.email, u.name
            FROM `account_activation_tokens` t
            JOIN `users` u ON u.id = t.user_id
            WHERE t.token_hash = :hash
              AND t.used_at IS NULL
              AND t.expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute(['hash' => $tokenHash]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function activateAccount(int $tokenId, int $userId, string $password): bool
    {
        return Database::transaction(function (PDO $pdo) use ($tokenId, $userId, $password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update user password and set status active
            $stmtUser = $pdo->prepare("UPDATE `users` SET `password` = :pwd, `status` = 'active' WHERE `id` = :id");
            $stmtUser->execute(['pwd' => $hashedPassword, 'id' => $userId]);

            // Mark token as used
            $stmtToken = $pdo->prepare("UPDATE `account_activation_tokens` SET `used_at` = NOW() WHERE `id` = :id");
            $stmtToken->execute(['id' => $tokenId]);

            return true;
        });
    }
}

