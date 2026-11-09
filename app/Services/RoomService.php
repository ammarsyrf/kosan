<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Models\Room;
use App\Repositories\RoomRepository;
use RuntimeException;

class RoomService
{
    private RoomRepository $roomRepo;

    public function __construct(?RoomRepository $roomRepo = null)
    {
        $this->roomRepo = $roomRepo ?? new RoomRepository();
    }

    public function getAllRooms(): array
    {
        return $this->roomRepo->getAll();
    }

    public function getRoomById(int $id): ?Room
    {
        return $this->roomRepo->findById($id);
    }

    public function createRoom(array $data, array $facilityIds = [], ?string $actor = 'System'): Room
    {
        // Enforce PRD rule: Kapasitas kurang dari 10 kamar
        $currentCount = $this->roomRepo->count();
        if ($currentCount >= 10) {
            throw new RuntimeException("Kapasitas maksimal tercapai. Sistem ini dikhususkan untuk kos dengan jumlah kurang dari 10 kamar.");
        }

        // Check room number uniqueness
        $existing = $this->roomRepo->findByNumber($data['room_number']);
        if ($existing) {
            throw new RuntimeException("Nomor kamar {$data['room_number']} sudah digunakan.");
        }

        $roomId = $this->roomRepo->create($data, $facilityIds);
        $room = $this->roomRepo->findById($roomId);

        Logger::audit((string)$actor, 'CREATE_ROOM', 'rooms', $roomId, [
            'room_number' => $data['room_number'],
            'price' => $data['current_price'],
        ]);

        return $room;
    }

    public function updateRoom(int $id, array $data, ?array $facilityIds = null, ?string $actor = 'System'): Room
    {
        $existing = $this->roomRepo->findById($id);
        if (!$existing) {
            throw new RuntimeException("Kamar tidak ditemukan.");
        }

        // Check room number conflict
        if ($data['room_number'] !== $existing->room_number) {
            $duplicate = $this->roomRepo->findByNumber($data['room_number']);
            if ($duplicate && $duplicate->id !== $id) {
                throw new RuntimeException("Nomor kamar {$data['room_number']} sudah digunakan.");
            }
        }

        $this->roomRepo->update($id, $data, $facilityIds);

        Logger::audit((string)$actor, 'UPDATE_ROOM', 'rooms', $id, [
            'old_price' => $existing->current_price,
            'new_price' => $data['current_price'],
        ]);

        return $this->roomRepo->findById($id);
    }

    public function deleteRoom(int $id, ?string $actor = 'System'): bool
    {
        $room = $this->roomRepo->findById($id);
        if (!$room) {
            throw new RuntimeException("Kamar tidak ditemukan.");
        }

        if ($room->status === 'occupied') {
            throw new RuntimeException("Kamar yang sedang berstatus terisi tidak dapat dihapus.");
        }

        $result = $this->roomRepo->delete($id);

        Logger::audit((string)$actor, 'DELETE_ROOM', 'rooms', $id, [
            'room_number' => $room->room_number,
        ]);

        return $result;
    }
}
