<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(?UserRepository $userRepo = null)
    {
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function index(Request $request): Response
    {
        $roleFilter = (string)$request->query('role', '');
        $allUsers = $this->userRepo->getAllUsers($roleFilter !== '' && $roleFilter !== 'all' ? $roleFilter : null);

        $stats = [
            'total' => count($this->userRepo->getAllUsers()),
            'owner' => count($this->userRepo->getAllUsers('owner')),
            'admin' => count($this->userRepo->getAllUsers('admin')),
            'tenant' => count($this->userRepo->getAllUsers('tenant')),
        ];

        return $this->render('admin/users/index', [
            'title' => 'Kelola Akun & Pengguna',
            'users' => $allUsers,
            'stats' => $stats,
            'currentFilter' => $roleFilter ?: 'all',
        ]);
    }

    public function create(): Response
    {
        return $this->render('admin/users/create', [
            'title' => 'Tambah Akun Pengelola / Admin',
            'roles' => $this->userRepo->getRoles(),
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'role' => 'required|in:admin,owner',
            'password' => 'required|min:6',
        ]);

        $email = strtolower(trim((string)$validated['email']));

        // Check if email already registered
        if ($this->userRepo->findByEmail($email)) {
            Session::flash('error', "Alamat email {$email} sudah terdaftar di sistem.");
            Session::flash('old', $request->all());
            return $this->redirect('/admin/users/create');
        }

        $currentUser = Session::get('user');

        try {
            $userId = $this->userRepo->createUser([
                'name' => (string)$validated['name'],
                'email' => $email,
                'phone' => (string)$request->post('phone', ''),
                'role' => (string)$validated['role'],
                'status' => (string)$request->post('status', 'active'),
                'password' => (string)$validated['password'],
            ]);

            Logger::audit((string)($currentUser['name'] ?? 'Owner'), 'CREATE_USER', 'users', $userId, [
                'name' => $validated['name'],
                'email' => $email,
                'role' => $validated['role'],
            ]);

            Session::flash('success', "Akun {$validated['name']} ({$validated['role']}) berhasil dibuat!");
            return $this->redirect('/admin/users');
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal membuat akun: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/users/create');
        }
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = $this->userRepo->findById($id);

        if (!$user) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect('/admin/users');
        }

        return $this->render('admin/users/edit', [
            'title' => 'Ubah Akun Pengguna',
            'targetUser' => $user,
            'roles' => $this->userRepo->getRoles(),
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $targetUser = $this->userRepo->findById($id);

        if (!$targetUser) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect('/admin/users');
        }

        $validated = $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $currentUser = Session::get('user');
        $email = strtolower(trim((string)$validated['email']));

        // Check if email already used by another user
        $existing = $this->userRepo->findByEmail($email);
        if ($existing && (int)$existing->id !== $id) {
            Session::flash('error', "Email {$email} sudah digunakan oleh pengguna lain.");
            return $this->redirect("/admin/users/{$id}/edit");
        }

        // Prevent owner from locking themselves out
        if ((int)$currentUser['id'] === $id && $validated['status'] !== 'active') {
            Session::flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang masuk.');
            return $this->redirect("/admin/users/{$id}/edit");
        }

        $payload = [
            'name' => (string)$validated['name'],
            'email' => $email,
            'phone' => (string)$request->post('phone', ''),
            'status' => (string)$validated['status'],
        ];

        // Role update if provided
        $newRole = (string)$request->post('role', '');
        if ($newRole !== '') {
            $payload['role'] = $newRole;
        }

        // Password update if provided
        $password = (string)$request->post('password', '');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                Session::flash('error', 'Kata sandi baru minimal 6 karakter.');
                return $this->redirect("/admin/users/{$id}/edit");
            }
            $payload['password'] = $password;
        }

        try {
            $this->userRepo->updateUser($id, $payload);

            Logger::audit((string)($currentUser['name'] ?? 'Owner'), 'UPDATE_USER', 'users', $id, [
                'name' => $validated['name'],
                'email' => $email,
                'role' => $newRole ?: $targetUser->role,
                'password_changed' => !empty($password),
            ]);

            Session::flash('success', "Data akun {$validated['name']} berhasil diperbarui!");
            return $this->redirect('/admin/users');
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal memperbarui akun: ' . $e->getMessage());
            return $this->redirect("/admin/users/{$id}/edit");
        }
    }

    public function resetPassword(Request $request): Response
    {
        $id = (int)$request->param('id');
        $targetUser = $this->userRepo->findById($id);

        if (!$targetUser) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect('/admin/users');
        }

        $newPassword = (string)$request->post('new_password', '');
        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Kata sandi baru minimal 6 karakter.');
            return $this->redirect('/admin/users');
        }

        $currentUser = Session::get('user');

        try {
            $this->userRepo->resetPassword($id, $newPassword);

            Logger::audit((string)($currentUser['name'] ?? 'Owner'), 'RESET_PASSWORD', 'users', $id, [
                'target_name' => $targetUser->name,
                'target_email' => $targetUser->email,
            ]);

            Session::flash('success', "Kata sandi akun {$targetUser->name} ({$targetUser->email}) berhasil diganti!");
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal mereset kata sandi: ' . $e->getMessage());
        }

        return $this->redirect('/admin/users');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id');
        $currentUser = Session::get('user');

        if ((int)$currentUser['id'] === $id) {
            Session::flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return $this->redirect('/admin/users');
        }

        $targetUser = $this->userRepo->findById($id);
        if (!$targetUser) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect('/admin/users');
        }

        try {
            $this->userRepo->deleteUser($id);

            Logger::audit((string)($currentUser['name'] ?? 'Owner'), 'DELETE_USER', 'users', $id, [
                'name' => $targetUser->name,
                'email' => $targetUser->email,
            ]);

            Session::flash('success', "Akun {$targetUser->name} berhasil dihapus.");
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }

        return $this->redirect('/admin/users');
    }
}
