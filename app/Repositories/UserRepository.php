<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "
            SELECT u.*, r.name AS role_name
            FROM users u
            LEFT JOIN role_user ru ON ru.user_id = u.id
            LEFT JOIN roles r ON r.id = ru.role_id
            WHERE u.email = :email AND u.status = 'active'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $row['role'] = $row['role_name'] ?? 'tenant';
        return new User($row);
    }

    public function findById(int $id): ?User
    {
        $sql = "
            SELECT u.*, r.name AS role_name
            FROM users u
            LEFT JOIN role_user ru ON ru.user_id = u.id
            LEFT JOIN roles r ON r.id = ru.role_id
            WHERE u.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $row['role'] = $row['role_name'] ?? 'tenant';
        return new User($row);
    }

    public function getUserPermissions(int $userId): array
    {
        $sql = "
            SELECT p.name
            FROM permissions p
            JOIN permission_role pr ON pr.permission_id = p.id
            JOIN role_user ru ON ru.role_id = pr.role_id
            WHERE ru.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function getAllUsers(?string $role = null): array
    {
        $sql = "
            SELECT u.*, r.name AS role_name, r.label AS role_label,
                   t.id AS tenant_id,
                   (SELECT ro.room_number FROM rental_contracts rc JOIN rooms ro ON ro.id = rc.room_id WHERE rc.tenant_id = t.id AND rc.status = 'active' LIMIT 1) AS room_number
            FROM users u
            LEFT JOIN role_user ru ON ru.user_id = u.id
            LEFT JOIN roles r ON r.id = ru.role_id
            LEFT JOIN tenants t ON t.user_id = u.id
        ";

        $params = [];
        if ($role !== null && $role !== '') {
            $sql .= " WHERE r.name = :role ";
            $params['role'] = $role;
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getRoles(): array
    {
        return $this->db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function createUser(array $data): int
    {
        return Database::transaction(function (PDO $pdo) use ($data) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO `users` (`name`, `email`, `password`, `phone`, `status`)
                VALUES (:name, :email, :password, :phone, :status)
            ");
            $stmt->execute([
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $hashedPassword,
                'phone' => trim($data['phone'] ?? ''),
                'status' => $data['status'] ?? 'active',
            ]);

            $userId = (int)$pdo->lastInsertId();

            // Attach role
            $roleName = $data['role'] ?? 'admin';
            $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = :name LIMIT 1");
            $roleStmt->execute(['name' => $roleName]);
            $roleId = $roleStmt->fetchColumn();

            if ($roleId) {
                $ruStmt = $pdo->prepare("INSERT INTO `role_user` (`user_id`, `role_id`) VALUES (?, ?)");
                $ruStmt->execute([$userId, (int)$roleId]);
            }

            return $userId;
        });
    }

    public function updateUser(int $id, array $data): bool
    {
        return Database::transaction(function (PDO $pdo) use ($id, $data) {
            $fields = [
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'phone' => trim($data['phone'] ?? ''),
                'status' => $data['status'] ?? 'active',
            ];

            $sql = "UPDATE `users` SET `name` = :name, `email` = :email, `phone` = :phone, `status` = :status";

            if (!empty($data['password'])) {
                $sql .= ", `password` = :password";
                $fields['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql .= " WHERE `id` = :id";
            $fields['id'] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($fields);

            // Update role if supplied
            if (!empty($data['role'])) {
                $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = :name LIMIT 1");
                $roleStmt->execute(['name' => $data['role']]);
                $roleId = $roleStmt->fetchColumn();

                if ($roleId) {
                    $pdo->prepare("DELETE FROM `role_user` WHERE `user_id` = ?")->execute([$id]);
                    $pdo->prepare("INSERT INTO `role_user` (`user_id`, `role_id`) VALUES (?, ?)")->execute([$id, (int)$roleId]);
                }
            }

            return true;
        });
    }

    public function resetPassword(int $userId, string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE `users` SET `password` = :pwd, `status` = 'active' WHERE `id` = :id");
        return $stmt->execute(['pwd' => $hashed, 'id' => $userId]);
    }

    public function deleteUser(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `users` WHERE `id` = :id");
        return $stmt->execute(['id' => $userId]);
    }
}

