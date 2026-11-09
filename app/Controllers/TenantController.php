<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\TenantService;

class TenantController extends Controller
{
    private TenantService $tenantService;

    public function __construct(?TenantService $tenantService = null)
    {
        $this->tenantService = $tenantService ?? new TenantService();
    }

    public function index(): Response
    {
        $tenants = $this->tenantService->getAllTenants();

        return $this->render('admin/tenants/index', [
            'title' => 'Data Penghuni (Tenants)',
            'tenants' => $tenants,
        ]);
    }

    public function create(): Response
    {
        return $this->render('admin/tenants/create', [
            'title' => 'Daftarkan Penghuni Baru',
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|max:30',
        ]);

        $user = Session::get('user');

        try {
            $result = $this->tenantService->createTenant([
                'name' => (string)$validated['name'],
                'email' => (string)$validated['email'],
                'phone' => (string)$validated['phone'],
                'id_card_number' => (string)$request->post('id_card_number', ''),
                'emergency_contact_name' => (string)$request->post('emergency_contact_name', ''),
                'emergency_contact_phone' => (string)$request->post('emergency_contact_phone', ''),
                'occupation' => (string)$request->post('occupation', ''),
                'notes' => (string)$request->post('notes', ''),
            ], $user['name'] ?? 'Admin');

            Session::flash('success', "Akun calon penghuni {$validated['name']} berhasil dibuat!");
            Session::flash('activation_url', $result['activation_url']);
            return $this->redirect('/admin/tenants');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/tenants/create');
        }
    }

    public function showActivationForm(Request $request): Response
    {
        $token = (string)$request->query('token', '');
        if (!$token) {
            return $this->render('auth/activation_failed', [
                'title' => 'Tautan Tidak Valid',
                'error' => 'Token aktivasi tidak disertakan.',
            ], 'layouts/auth');
        }

        return $this->render('auth/activate', [
            'title' => 'Aktivasi Akun Penghuni',
            'token' => $token,
        ], 'layouts/auth');
    }

    public function activate(Request $request): Response
    {
        $validated = $this->validate($request, [
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $this->tenantService->activateTenant(
                (string)$validated['token'],
                (string)$validated['password']
            );

            Session::flash('success', 'Akun Anda telah aktif! Silakan masuk dengan email dan kata sandi baru Anda.');
            return $this->redirect('/login');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/activate?token=' . urlencode((string)$validated['token']));
        }
    }
}

