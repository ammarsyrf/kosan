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

        try {
            $this->roomService->createRoom([
                'room_number' => (string)$validated['room_number'],
                'name' => (string)$validated['name'],
                'current_price' => (float)$validated['current_price'],
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

        try {
            $this->roomService->updateRoom($id, [
                'room_number' => (string)$validated['room_number'],
                'name' => (string)$validated['name'],
                'current_price' => (float)$validated['current_price'],
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
