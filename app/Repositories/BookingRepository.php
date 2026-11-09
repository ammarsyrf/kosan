<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Booking;
use PDO;

class BookingRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT b.*, r.room_number, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `bookings` b
            JOIN `rooms` r ON r.id = b.room_id
            JOIN `tenants` t ON t.id = b.tenant_id
            JOIN `users` u ON u.id = t.user_id
            ORDER BY b.created_at DESC
        ";

        $rows = $this->db->query($sql)->fetchAll();
        return array_map(function ($row) {
            $booking = new Booking($row);
            $booking->room_number = $row['room_number'];
            $booking->tenant_name = $row['tenant_name'];
            $booking->tenant_phone = $row['tenant_phone'] ?? '';
            return $booking;
        }, $rows);
    }

    public function getByTenantId(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, r.room_number, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `bookings` b
            JOIN `rooms` r ON r.id = b.room_id
            JOIN `tenants` t ON t.id = b.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE b.tenant_id = :tenant_id
            ORDER BY b.created_at DESC
        ");
        $stmt->execute(['tenant_id' => $tenantId]);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $booking = new Booking($row);
            $booking->room_number = $row['room_number'];
            $booking->tenant_name = $row['tenant_name'];
            $booking->tenant_phone = $row['tenant_phone'] ?? '';
            return $booking;
        }, $rows);
    }

    public function findById(int $id): ?Booking
    {
        $stmt = $this->db->prepare("
            SELECT b.*, r.room_number, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `bookings` b
            JOIN `rooms` r ON r.id = b.room_id
            JOIN `tenants` t ON t.id = b.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE b.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $booking = new Booking($row);
        $booking->room_number = $row['room_number'];
        $booking->tenant_name = $row['tenant_name'];
        $booking->tenant_phone = $row['tenant_phone'] ?? '';
        return $booking;
    }

    /**
     * Checks if a room is available for the given date range.
     * Must be called inside a transaction with room row locked.
     */
    public function isRoomAvailable(int $roomId, string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        // 1. Check room operational status
        $stmtRoom = $this->db->prepare("SELECT `status` FROM `rooms` WHERE `id` = :room_id LIMIT 1");
        $stmtRoom->execute(['room_id' => $roomId]);
        $roomStatus = $stmtRoom->fetchColumn();

        if (!$roomStatus || in_array($roomStatus, ['maintenance', 'inactive'], true)) {
            return false;
        }

        // 2. Check overlap with active rental contracts: [startDate, endDate] overlaps [c.start_date, c.end_date]
        $sqlContract = "
            SELECT COUNT(*) FROM `rental_contracts`
            WHERE `room_id` = :room_id
              AND `status` = 'active'
              AND :start_date <= `end_date`
              AND :end_date >= `start_date`
        ";
        $stmtContract = $this->db->prepare($sqlContract);
        $stmtContract->execute([
            'room_id' => $roomId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        if ((int)$stmtContract->fetchColumn() > 0) {
            return false;
        }

        // 3. Check overlap with active bookings (confirmed, or pending with non-expired time)
        $sqlBooking = "
            SELECT COUNT(*) FROM `bookings`
            WHERE `room_id` = :room_id
              AND `id` != :exclude_id
              AND (
                  `status` = 'confirmed'
                  OR (`status` = 'pending' AND `expires_at` > NOW())
              )
              AND :start_date <= `end_date`
              AND :end_date >= `start_date`
        ";
        $stmtBooking = $this->db->prepare($sqlBooking);
        $stmtBooking->execute([
            'room_id' => $roomId,
            'exclude_id' => $excludeBookingId ?? 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return (int)$stmtBooking->fetchColumn() === 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `bookings` (
                `room_id`, `tenant_id`, `start_date`, `end_date`, `status`,
                `duration_hours_snapshot`, `expires_at`, `notes`
            ) VALUES (
                :room_id, :tenant_id, :start_date, :end_date, :status,
                :duration_hours_snapshot, :expires_at, :notes
            )
        ");

        $stmt->execute([
            'room_id' => $data['room_id'],
            'tenant_id' => $data['tenant_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'] ?? 'pending',
            'duration_hours_snapshot' => $data['duration_hours_snapshot'] ?? 24,
            'expires_at' => $data['expires_at'],
            'notes' => $data['notes'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $sql = "UPDATE `bookings` SET `status` = :status";
        $params = ['id' => $id, 'status' => $status];

        if ($notes !== null) {
            $sql .= ", `notes` = :notes";
            $params['notes'] = $notes;
        }

        $sql .= " WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function expirePendingBookings(): int
    {
        $stmt = $this->db->prepare("
            UPDATE `bookings`
            SET `status` = 'expired'
            WHERE `status` = 'pending'
              AND `expires_at` <= NOW()
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }
}

