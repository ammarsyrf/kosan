<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\FacilityRepository;
use App\Services\RoomService;

class RoomController extends Controller
{
    private RoomService $roomService;
    private FacilityRepository $facilityRepo;

    public function __construct(?RoomService $roomService = null, ?FacilityRepository $facilityRepo = null)
    {
        $this->roomService = $roomService ?? new RoomService();
        $this->facilityRepo = $facilityRepo ?? new FacilityRepository();
    }

    public function index(): Response
    {
        $rooms = $this->roomService->getAllRooms();

        return $this->render('admin/rooms/index', [
            'title' => 'Manajemen Kamar — Kost Management',
            'rooms' => $rooms,
        ]);
    }

    public function create(): Response
    {
        $facilities = $this->facilityRepo->getAll();

        return $this->render('admin/rooms/create', [
            'title' => 'Tambah Kamar Baru',
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'room_number' => 'required|max:20',
            'name' => 'required|max:100',
            'current_price' => 'required|numeric',
            'status' => 'required|in:available,occupied,maintenance,inactive',
        ]);

        $facilityIds = (array)$request->post('facilities', []);
        $user = Session::get('user');

        $photoUrl = $request->post('photo_url') ? trim((string)$request->post('photo_url')) : null;
        if (!empty($_FILES['photo']['tmp_name']) && (is_uploaded_file($_FILES['photo']['tmp_name']) || file_exists($_FILES['photo']['tmp_name']))) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $dir = BASE_PATH . '/public/images/rooms';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'room-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$validated['room_number']) . '.' . $ext;
                $target = $dir . '/' . $filename;
                if (@move_uploaded_file($_FILES['photo']['tmp_name'], $target) || @copy($_FILES['photo']['tmp_name'], $target)) {
                    $photoUrl = 'images/rooms/' . $filename;
                }
            }
        }

        try {
            $this->roomService->createRoom([
                'room_number' => (string)$validated['room_number'],
                'floor' => (int)$request->post('floor', str_starts_with((string)$validated['room_number'], '2') ? 2 : 1),
                'name' => (string)$validated['name'],
                'size' => (string)$request->post('size', '3x4 m²'),
                'current_price' => (float)$validated['current_price'],
                'photo_url' => $photoUrl,
                'status' => (string)$validated['status'],
                'description' => (string)$request->post('description', ''),
                'notes' => (string)$request->post('notes', ''),
            ], $facilityIds, $user['name'] ?? 'Admin');

            Session::flash('success', "Kamar {$validated['room_number']} berhasil ditambahkan.");
            return $this->redirect('/admin/rooms');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/rooms/create');
        }
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->param('id');
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            Session::flash('error', 'Kamar tidak ditemukan.');
            return $this->redirect('/admin/rooms');
        }

        $facilities = $this->facilityRepo->getAll();
        $assignedIds = array_column($room->facilities, 'id');

        return $this->render('admin/rooms/edit', [
            'title' => "Ubah Kamar {$room->room_number}",
            'room' => $room,
            'facilities' => $facilities,
            'assignedIds' => $assignedIds,
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $validated = $this->validate($request, [
            'room_number' => 'required|max:20',
            'name' => 'required|max:100',
            'current_price' => 'required|numeric',
            'status' => 'required|in:available,occupied,maintenance,inactive',
        ]);

        $facilityIds = (array)$request->post('facilities', []);
        $user = Session::get('user');

        $existing = $this->roomService->getRoomById($id);
        $photoUrl = $existing?->photo_url;
        if ($request->post('photo_url') !== null && trim((string)$request->post('photo_url')) !== '') {
            $photoUrl = trim((string)$request->post('photo_url'));
        }
        if (!empty($_FILES['photo']['tmp_name']) && (is_uploaded_file($_FILES['photo']['tmp_name']) || file_exists($_FILES['photo']['tmp_name']))) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $dir = BASE_PATH . '/public/images/rooms';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'room-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$validated['room_number']) . '.' . $ext;
                $target = $dir . '/' . $filename;
                if (@move_uploaded_file($_FILES['photo']['tmp_name'], $target) || @copy($_FILES['photo']['tmp_name'], $target)) {
                    $photoUrl = 'images/rooms/' . $filename;
                }
            }
        }

        try {
            $this->roomService->updateRoom($id, [
                'room_number' => (string)$validated['room_number'],
                'floor' => (int)$request->post('floor', str_starts_with((string)$validated['room_number'], '2') ? 2 : 1),
                'name' => (string)$validated['name'],
                'size' => (string)$request->post('size', '3x4 m²'),
                'current_price' => (float)$validated['current_price'],
                'photo_url' => $photoUrl,
                'status' => (string)$validated['status'],
                'description' => (string)$request->post('description', ''),
                'notes' => (string)$request->post('notes', ''),
            ], $facilityIds, $user['name'] ?? 'Admin');

            Session::flash('success', "Data kamar {$validated['room_number']} berhasil diperbarui.");
            return $this->redirect('/admin/rooms');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect("/admin/rooms/{$id}/edit");
        }
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');

        try {
            $this->roomService->deleteRoom($id, $user['name'] ?? 'Admin');
            Session::flash('success', 'Kamar berhasil dihapus.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/rooms');
    }
}

