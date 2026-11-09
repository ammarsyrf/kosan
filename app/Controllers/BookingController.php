<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Services\BookingService;

class BookingController extends Controller
{
    private BookingService $bookingService;
    private RoomRepository $roomRepo;
    private TenantRepository $tenantRepo;

    public function __construct(
        ?BookingService $bookingService = null,
        ?RoomRepository $roomRepo = null,
        ?TenantRepository $tenantRepo = null
    ) {
        $this->bookingService = $bookingService ?? new BookingService();
        $this->roomRepo = $roomRepo ?? new RoomRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
    }

    public function index(): Response
    {
        $bookings = $this->bookingService->getAllBookings();

        return $this->render('admin/bookings/index', [
            'title' => 'Daftar Booking Kamar',
            'bookings' => $bookings,
        ]);
    }

    public function create(): Response
    {
        $rooms = $this->roomRepo->getAvailableRooms();
        $tenants = $this->tenantRepo->getAll();

        return $this->render('admin/bookings/create', [
            'title' => 'Buat Booking Kamar Baru',
            'rooms' => $rooms,
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'room_id' => 'required|numeric',
            'tenant_id' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $user = Session::get('user');

        try {
            $booking = $this->bookingService->createBooking([
                'room_id' => (int)$validated['room_id'],
                'tenant_id' => (int)$validated['tenant_id'],
                'start_date' => (string)$validated['start_date'],
                'end_date' => (string)$validated['end_date'],
                'notes' => (string)$request->post('notes', ''),
            ], $user['name'] ?? 'Admin');

            Session::flash('success', "Booking kamar {$booking->room_number} berhasil dibuat (Status: Pending, Berlaku 24 Jam).");
            return $this->redirect('/admin/bookings');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/bookings/create');
        }
    }

    public function confirm(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');

        try {
            $this->bookingService->confirmBooking($id, $user['name'] ?? 'Admin');
            Session::flash('success', 'Booking berhasil dikonfirmasi (Status: Confirmed).');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/bookings');
    }

    public function cancel(Request $request): Response
    {
        $id = (int)$request->param('id');
        $reason = (string)$request->post('reason', 'Dibatalkan oleh admin');
        $user = Session::get('user');

        try {
            $this->bookingService->cancelBooking($id, $reason, $user['name'] ?? 'Admin');
            Session::flash('success', 'Booking berhasil dibatalkan.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/bookings');
    }

    public function convert(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');

        try {
            $contractId = $this->bookingService->convertToContract($id, [
                'notes' => (string)$request->post('notes', 'Dikonversi dari booking'),
            ], $user['id'] ?? null, $user['name'] ?? 'Admin');

            Session::flash('success', 'Booking berhasil dikonversi menjadi Kontrak Sewa Aktif!');
            return $this->redirect("/admin/contracts/{$contractId}");
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/admin/bookings');
        }
    }
}

