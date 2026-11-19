<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class HomeController extends Controller
{
    public function index(): Response
    {
        $user = Session::get('user');
        if ($user) {
            $role = strtolower($user['role'] ?? '');
            if ($role === 'owner') {
                return $this->redirect('/owner/dashboard');
            } elseif ($role === 'admin') {
                return $this->redirect('/admin/dashboard');
            } elseif ($role === 'tenant') {
                return $this->redirect('/tenant/dashboard');
            }
        }

        $rooms = [];
        $stats = [
            'total_rooms' => 8,
            'occupied' => 6,
            'available' => 2,
            'maintenance' => 0,
            'occupancy_rate' => 75.0,
        ];

        try {
            $roomRepo = new \App\Repositories\RoomRepository();
            $rooms = $roomRepo->getAll();
            if (!empty($rooms)) {
                $total = count($rooms);
                $occupied = count(array_filter($rooms, fn($r) => $r->status === 'occupied'));
                $available = count(array_filter($rooms, fn($r) => $r->status === 'available'));
                $maintenance = count(array_filter($rooms, fn($r) => $r->status === 'maintenance'));
                $stats = [
                    'total_rooms' => $total,
                    'occupied' => $occupied,
                    'available' => $available,
                    'maintenance' => $maintenance,
                    'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0.0,
                ];
            }
        } catch (\Throwable $e) {
            // graceful fallback
        }

        return $this->render('home', [
            'title' => 'Sistem Manajemen Kos Modern & Terpadu — ' . setting('business_name', config('app.name', 'Kost Zenerie')),
            'user' => $user,
            'rooms' => $rooms,
            'stats' => $stats,
            'is_landing' => true,
        ]);
    }

    public function catalog(): Response
    {
        $rooms = [];
        $facilities = [];
        $stats = [
            'total_rooms' => 0,
            'available' => 0,
            'occupied' => 0,
            'min_price' => 0,
            'max_price' => 0,
        ];

        try {
            $roomRepo = new \App\Repositories\RoomRepository();
            $rooms = $roomRepo->getAll();

            $facilityRepo = new \App\Repositories\FacilityRepository();
            $facilities = $facilityRepo->getAll();

            if (!empty($rooms)) {
                $prices = array_map(fn($r) => (float)$r->current_price, $rooms);
                $stats = [
                    'total_rooms' => count($rooms),
                    'available' => count(array_filter($rooms, fn($r) => ($r->status ?? 'available') === 'available')),
                    'occupied' => count(array_filter($rooms, fn($r) => ($r->status ?? '') === 'occupied')),
                    'min_price' => !empty($prices) ? min($prices) : 0,
                    'max_price' => !empty($prices) ? max($prices) : 0,
                ];
            }
        } catch (\Throwable $e) {
            // graceful fallback
        }

        return $this->render('catalog', [
            'title' => 'Katalog Lengkap Kamar Kost — ' . setting('business_name', config('app.name', 'Kost Zenerie')),
            'rooms' => $rooms,
            'facilities' => $facilities,
            'stats' => $stats,
            'user' => Session::get('user'),
            'is_landing' => true,
        ]);
    }

    public function roomDetail(Request $request): Response
    {
        $idOrNum = $request->param('id');
        $roomRepo = new \App\Repositories\RoomRepository();

        $room = is_numeric($idOrNum)
            ? ($roomRepo->findById((int)$idOrNum) ?? $roomRepo->findByNumber((string)$idOrNum))
            : $roomRepo->findByNumber((string)$idOrNum);

        if (!$room) {
            Session::flash('error', 'Kamar tidak ditemukan.');
            return $this->redirect('/katalog');
        }

        if ($request->isJson() || $request->query('format') === 'json') {
            return Response::json([
                'success' => true,
                'room' => $room,
            ]);
        }

        $allRooms = $roomRepo->getAll();
        $recommendations = array_values(array_filter(
            $allRooms,
            fn($r) => $r->id !== $room->id && ($r->status ?? '') === 'available'
        ));

        return $this->render('room_detail', [
            'title' => "{$room->name} — " . setting('business_name', 'Kost Zenerie'),
            'room' => $room,
            'recommendations' => array_slice($recommendations, 0, 4),
            'user' => Session::get('user'),
            'is_landing' => true,
        ]);
    }

    public function ownerDashboard(): Response
    {
        return $this->render('home', [
            'title' => 'Dashboard Owner',
            'role_zone' => 'Owner Area (Verified via RoleMiddleware)',
            'user' => Session::get('user'),
        ]);
    }

    public function adminDashboard(): Response
    {
        return $this->render('home', [
            'title' => 'Dashboard Admin',
            'role_zone' => 'Admin Area (Verified via RoleMiddleware)',
            'user' => Session::get('user'),
        ]);
    }

    public function tenantDashboard(): Response
    {
        return $this->render('home', [
            'title' => 'Portal Tenant',
            'role_zone' => 'Tenant Area (Verified via RoleMiddleware)',
            'user' => Session::get('user'),
        ]);
    }
}

