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
}

