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

    public function edit(Request $request): Response
    {
        $id = (int)$request->param('id');
        $tenant = $this->tenantService->getTenantById($id);

        if (!$tenant) {
            Session::flash('error', 'Data penghuni tidak ditemukan.');
            return $this->redirect('/admin/tenants');
        }

        return $this->render('admin/tenants/edit', [
            'title' => 'Ubah Data Penghuni',
            'tenant' => $tenant,
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $validated = $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|max:30',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $user = Session::get('user');
        $password = (string)$request->post('password', '');

        try {
            $payload = [
                'name' => (string)$validated['name'],
                'email' => (string)$validated['email'],
                'phone' => (string)$validated['phone'],
                'status' => (string)$validated['status'],
                'id_card_number' => (string)$request->post('id_card_number', ''),
                'emergency_contact_name' => (string)$request->post('emergency_contact_name', ''),
                'emergency_contact_phone' => (string)$request->post('emergency_contact_phone', ''),
                'occupation' => (string)$request->post('occupation', ''),
                'notes' => (string)$request->post('notes', ''),
            ];

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    throw new \RuntimeException('Kata sandi baru minimal 6 karakter.');
                }
                $payload['password'] = $password;
            }

            $this->tenantService->updateTenant($id, $payload, $user['name'] ?? 'Admin');

            Session::flash('success', "Data penghuni {$validated['name']} berhasil diperbarui!" . (!empty($password) ? " Kata sandi akun juga berhasil diubah." : ""));
            return $this->redirect('/admin/tenants');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect("/admin/tenants/{$id}/edit");
        }
    }

    public function resetPassword(Request $request): Response
    {
        $id = (int)$request->param('id');
        $newPassword = (string)$request->post('new_password', '');
        $user = Session::get('user');

        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Kata sandi baru minimal 6 karakter.');
            return $this->redirect('/admin/tenants');
        }

        try {
            $this->tenantService->resetPassword($id, $newPassword, $user['name'] ?? 'Owner');
            Session::flash('success', 'Kata sandi akun penghuni berhasil direset!');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/tenants');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');

        try {
            $this->tenantService->deleteTenant($id, $user['name'] ?? 'Admin');
            Session::flash('success', 'Data penghuni berhasil dihapus.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/tenants');
    }
}

