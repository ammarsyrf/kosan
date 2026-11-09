<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
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
            'title' => 'Sistem Manajemen Kos Modern & Terpadu — ' . config('app.name', 'Kost Management System'),
            'user' => $user,
            'rooms' => $rooms,
            'stats' => $stats,
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

