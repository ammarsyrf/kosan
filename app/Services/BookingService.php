<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\ContractRepository;
use PDO;
use RuntimeException;

class BookingService
{
    private BookingRepository $bookingRepo;
    private ContractRepository $contractRepo;

    public function __construct(
        ?BookingRepository $bookingRepo = null,
        ?ContractRepository $contractRepo = null
    ) {
        $this->bookingRepo = $bookingRepo ?? new BookingRepository();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
    }

    public function getAllBookings(): array
    {
        return $this->bookingRepo->getAll();
    }

    public function getBookingById(int $id): ?Booking
    {
        return $this->bookingRepo->findById($id);
    }

    public function getTenantBookings(int $tenantId): array
    {
        return $this->bookingRepo->getByTenantId($tenantId);
    }

    public function createBooking(array $data, ?string $actor = 'System'): Booking
    {
        return Database::transaction(function (PDO $pdo) use ($data, $actor) {
            $roomId = (int)$data['room_id'];
            $startDate = (string)$data['start_date'];
            $endDate = (string)$data['end_date'];

            if ($startDate >= $endDate) {
                throw new RuntimeException("Tanggal mulai sewa harus lebih awal daripada tanggal selesai.");
            }

            // 1. Lock room row with SELECT ... FOR UPDATE to avoid race condition
            $stmtLock = $pdo->prepare("SELECT `id`, `status` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $roomId]);
            $room = $stmtLock->fetch();

            if (!$room) {
                throw new RuntimeException("Kamar tidak ditemukan.");
            }

            if (in_array($room['status'], ['maintenance', 'inactive'], true)) {
                throw new RuntimeException("Kamar sedang dalam masa pemeliharaan atau tidak aktif.");
            }

            // 2. Check overlap inside transaction
            $isAvailable = $this->bookingRepo->isRoomAvailable($roomId, $startDate, $endDate);
            if (!$isAvailable) {
                throw new RuntimeException("Kamar tidak tersedia pada rentang tanggal yang dipilih karena sudah dibooking atau memiliki kontrak aktif.");
            }

            // 3. Read default booking duration from settings table (PRD: configurable)
            $stmtDuration = $pdo->query("SELECT `value` FROM `settings` WHERE `key` = 'booking_duration_hours' LIMIT 1");
            $durationHours = (int)($stmtDuration->fetchColumn() ?: 24);

            $expiresAt = date('Y-m-d H:i:s', time() + ($durationHours * 3600));

            // 4. Create booking
            $bookingId = $this->bookingRepo->create([
                'room_id' => $roomId,
                'tenant_id' => (int)$data['tenant_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending',
                'duration_hours_snapshot' => $durationHours,
                'expires_at' => $expiresAt,
                'notes' => $data['notes'] ?? null,
            ]);

            Logger::audit((string)$actor, 'CREATE_BOOKING', 'bookings', $bookingId, [
                'room_id' => $roomId,
                'duration_hours' => $durationHours,
                'expires_at' => $expiresAt,
            ]);

            return $this->bookingRepo->findById($bookingId);
        });
    }

    public function confirmBooking(int $id, ?string $actor = 'Admin'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($id, $actor) {
            $booking = $this->bookingRepo->findById($id);
            if (!$booking) {
                throw new RuntimeException("Booking tidak ditemukan.");
            }

            if ($booking->status === 'cancelled') {
                throw new RuntimeException("Booking yang telah dibatalkan tidak dapat dikonfirmasi.");
            }

            if ($booking->isExpired()) {
                throw new RuntimeException("Booking telah kedaluwarsa dan tidak dapat dikonfirmasi.");
            }

            $this->bookingRepo->updateStatus($id, 'confirmed');

            Logger::audit((string)$actor, 'CONFIRM_BOOKING', 'bookings', $id);
            return true;
        });
    }

    public function cancelBooking(int $id, ?string $reason = null, ?string $actor = 'System'): bool
    {
        return Database::transaction(function (PDO $pdo) use ($id, $reason, $actor) {
            $booking = $this->bookingRepo->findById($id);
            if (!$booking) {
                throw new RuntimeException("Booking tidak ditemukan.");
            }

            if ($booking->status === 'converted') {
                throw new RuntimeException("Booking yang sudah dikonversi menjadi kontrak tidak dapat dibatalkan.");
            }

            $this->bookingRepo->updateStatus($id, 'cancelled', $reason ? "Dibatalkan: {$reason}" : null);

            Logger::audit((string)$actor, 'CANCEL_BOOKING', 'bookings', $id, ['reason' => $reason]);
            return true;
        });
    }

    public function convertToContract(int $bookingId, array $contractData, ?int $adminId = null, ?string $actor = 'Admin'): int
    {
        return Database::transaction(function (PDO $pdo) use ($bookingId, $contractData, $adminId, $actor) {
            $booking = $this->bookingRepo->findById($bookingId);
            if (!$booking) {
                throw new RuntimeException("Booking tidak ditemukan.");
            }

            if ($booking->status === 'converted') {
                throw new RuntimeException("Booking ini sudah pernah dikonversi menjadi kontrak.");
            }

            if ($booking->status === 'cancelled' || $booking->isExpired()) {
                throw new RuntimeException("Booking yang dibatalkan atau kedaluwarsa tidak dapat dikonversi.");
            }

            // Lock room
            $stmtLock = $pdo->prepare("SELECT `id`, `current_price` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $booking->room_id]);
            $room = $stmtLock->fetch();

            // Check availability excluding this booking
            $isAvail = $this->bookingRepo->isRoomAvailable($booking->room_id, $booking->start_date, $booking->end_date, $bookingId);
            if (!$isAvail) {
                throw new RuntimeException("Kamar tidak tersedia untuk dikonversi menjadi kontrak.");
            }

            // Snapshot price from input or fallback to room's current price
            $rentPrice = isset($contractData['rent_price_snapshot']) && (float)$contractData['rent_price_snapshot'] > 0
                ? (float)$contractData['rent_price_snapshot']
                : (float)$room['current_price'];

            // Anchor day: Day of start_date (e.g. 2026-03-31 -> 31)
            $startDay = (int)date('j', strtotime($booking->start_date));
            $anchorDay = isset($contractData['billing_anchor_day']) ? (int)$contractData['billing_anchor_day'] : $startDay;

            $contractId = $this->contractRepo->createContract([
                'tenant_id' => $booking->tenant_id,
                'room_id' => $booking->room_id,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'rent_price_snapshot' => $rentPrice,
                'billing_anchor_day' => $anchorDay,
                'notes' => $contractData['notes'] ?? "Dikonversi dari booking #{$bookingId}",
            ], $adminId);

            // Mark booking as converted
            $this->bookingRepo->updateStatus($bookingId, 'converted');

            Logger::audit((string)$actor, 'CONVERT_BOOKING_TO_CONTRACT', 'rental_contracts', $contractId, [
                'booking_id' => $bookingId,
                'rent_price_snapshot' => $rentPrice,
                'billing_anchor_day' => $anchorDay,
            ]);

            return $contractId;
        });
    }

    public function expirePendingBookings(): int
    {
        return $this->bookingRepo->expirePendingBookings();
    }
}

