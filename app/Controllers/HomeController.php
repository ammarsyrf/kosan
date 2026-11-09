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

        return $this->render('home', [
            'title' => 'Beranda — Kost Management System',
            'user' => $user,
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

