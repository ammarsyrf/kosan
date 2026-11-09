<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        $this->authService = $authService ?? new AuthService();
    }

    public function showLogin(): Response
    {
        if ($this->authService->check()) {
            return $this->redirectBasedOnRole();
        }

        return $this->render('auth/login', [
            'title' => 'Masuk — Kost Management System',
        ], 'layouts/auth');
    }

    public function login(Request $request): Response
    {
        $validated = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->authService->attempt(
            (string)$validated['email'],
            (string)$validated['password'],
            $request->ip()
        );

        if (!$result['success']) {
            Session::flash('error', $result['error']);
            Session::flash('old', ['email' => $validated['email']]);
            return $this->redirect('/login');
        }

        Session::flash('success', 'Selamat datang kembali!');

        $intended = Session::get('intended_url');
        if ($intended) {
            Session::remove('intended_url');
            return $this->redirect($intended);
        }

        return $this->redirectBasedOnRole();
    }

    public function logout(): Response
    {
        $this->authService->logout();
        Session::flash('success', 'Anda telah berhasil keluar.');
        return $this->redirect('/login');
    }

    private function redirectBasedOnRole(): Response
    {
        $user = $this->authService->user();
        $role = strtolower((string)($user['role'] ?? ''));

        return match ($role) {
            'owner' => $this->redirect('/owner/dashboard'),
            'admin' => $this->redirect('/admin/dashboard'),
            'tenant' => $this->redirect('/tenant/dashboard'),
            default => $this->redirect('/'),
        };
    }
}

