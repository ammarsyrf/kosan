<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Facility;
use PDO;

class FacilityRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM `room_facilities` ORDER BY `name` ASC");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Facility($row), $rows);
    }

    public function findById(int $id): ?Facility
    {
        $stmt = $this->db->prepare("SELECT * FROM `room_facilities` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? new Facility($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `room_facilities` (`name`, `icon`, `description`)
            VALUES (:name, :icon, :description)
        ");
        $stmt->execute([
            'name' => trim($data['name']),
            'icon' => trim($data['icon'] ?? 'check'),
            'description' => trim($data['description'] ?? ''),
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `room_facilities`
            SET `name` = :name, `icon` = :icon, `description` = :description
            WHERE `id` = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => trim($data['name']),
            'icon' => trim($data['icon'] ?? 'check'),
            'description' => trim($data['description'] ?? ''),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `room_facilities` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }
}

