<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Room;
use PDO;

class RoomRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM `rooms` ORDER BY `room_number` ASC";
        $rows = $this->db->query($sql)->fetchAll();

        $rooms = [];
        foreach ($rows as $row) {
            $room = new Room($row);
            $room->facilities = $this->getFacilitiesForRoom($room->id);
            $rooms[] = $room;
        }

        return $rooms;
    }

    public function findById(int $id): ?Room
    {
        $stmt = $this->db->prepare("SELECT * FROM `rooms` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $room = new Room($row);
        $room->facilities = $this->getFacilitiesForRoom($room->id);
        return $room;
    }

    public function findByNumber(string $roomNumber): ?Room
    {
        $stmt = $this->db->prepare("SELECT * FROM `rooms` WHERE `room_number` = :rn LIMIT 1");
        $stmt->execute(['rn' => $roomNumber]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $room = new Room($row);
        $room->facilities = $this->getFacilitiesForRoom($room->id);
        return $room;
    }

    public function getFacilitiesForRoom(int $roomId): array
    {
        $stmt = $this->db->prepare("
            SELECT rf.*
            FROM `room_facilities` rf
            JOIN `room_facility_assignments` rfa ON rfa.facility_id = rf.id
            WHERE rfa.room_id = :room_id
            ORDER BY rf.name ASC
        ");
        $stmt->execute(['room_id' => $roomId]);
        return $stmt->fetchAll();
    }

    public function create(array $data, array $facilityIds = []): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `rooms` (`room_number`, `floor`, `name`, `size`, `description`, `current_price`, `photo_url`, `status`, `notes`)
            VALUES (:room_number, :floor, :name, :size, :description, :current_price, :photo_url, :status, :notes)
        ");

        $stmt->execute([
            'room_number' => $data['room_number'],
            'floor' => (int)($data['floor'] ?? (str_starts_with((string)$data['room_number'], '2') ? 2 : 1)),
            'name' => $data['name'],
            'size' => $data['size'] ?? '3x4 m²',
            'description' => $data['description'] ?? null,
            'current_price' => $data['current_price'],
            'photo_url' => $data['photo_url'] ?? null,
            'status' => $data['status'] ?? 'available',
            'notes' => $data['notes'] ?? null,
        ]);

        $roomId = (int)$this->db->lastInsertId();

        if (!empty($facilityIds)) {
            $this->syncFacilities($roomId, $facilityIds);
        }

        return $roomId;
    }

    public function update(int $id, array $data, ?array $facilityIds = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `rooms`
            SET `room_number` = :room_number,
                `floor` = :floor,
                `name` = :name,
                `size` = :size,
                `description` = :description,
                `current_price` = :current_price,
                `photo_url` = :photo_url,
                `status` = :status,
                `notes` = :notes
            WHERE `id` = :id
        ");

        $success = $stmt->execute([
            'id' => $id,
            'room_number' => $data['room_number'],
            'floor' => (int)($data['floor'] ?? (str_starts_with((string)$data['room_number'], '2') ? 2 : 1)),
            'name' => $data['name'],
            'size' => $data['size'] ?? '3x4 m²',
            'description' => $data['description'] ?? null,
            'current_price' => $data['current_price'],
            'photo_url' => $data['photo_url'] ?? null,
            'status' => $data['status'] ?? 'available',
            'notes' => $data['notes'] ?? null,
        ]);

        if ($facilityIds !== null) {
            $this->syncFacilities($id, $facilityIds);
        }

        return $success;
    }

    public function syncFacilities(int $roomId, array $facilityIds): void
    {
        $del = $this->db->prepare("DELETE FROM `room_facility_assignments` WHERE `room_id` = :room_id");
        $del->execute(['room_id' => $roomId]);

        if (!empty($facilityIds)) {
            $ins = $this->db->prepare("INSERT IGNORE INTO `room_facility_assignments` (`room_id`, `facility_id`) VALUES (?, ?)");
            foreach ($facilityIds as $fId) {
                $ins->execute([$roomId, (int)$fId]);
            }
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `rooms` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM `rooms`")->fetchColumn();
    }

    public function getAvailableRooms(): array
    {
        $stmt = $this->db->query("SELECT * FROM `rooms` WHERE `status` = 'available' ORDER BY `room_number` ASC");
        $rows = $stmt->fetchAll();

        $rooms = [];
        foreach ($rows as $row) {
            $room = new Room($row);
            $room->facilities = $this->getFacilitiesForRoom($room->id);
            $rooms[] = $room;
        }

        return $rooms;
    }
}

