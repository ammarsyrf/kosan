<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Core\Session;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    private ?UserRepository $userRepository;
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 900; // 15 minutes

    public function __construct(?UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository;
    }

    private function getUserRepo(): UserRepository
    {
        if ($this->userRepository === null) {
            $this->userRepository = new UserRepository();
        }
        return $this->userRepository;
    }

    public function attempt(string $email, string $password, string $ip = '127.0.0.1'): array
    {
        $throttleKey = $this->getThrottleKey($email, $ip);

        if ($this->isTooManyAttempts($throttleKey)) {
            $secondsRemaining = $this->getTimeRemaining($throttleKey);
            Logger::warning("Login rate limit exceeded for {$email} from {$ip}");
            return [
                'success' => false,
                'error' => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$secondsRemaining} detik.",
            ];
        }

        try {
            $user = $this->getUserRepo()->findByEmail($email);
        } catch (\Throwable $e) {
            $this->incrementAttempts($throttleKey);
            Logger::error("Database error during login: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Koneksi database belum siap. Mohon periksa konfigurasi database.",
            ];
        }

        if (!$user || !password_verify($password, $user->password)) {
            $this->incrementAttempts($throttleKey);
            Logger::warning("Failed login attempt for email: {$email} from IP: {$ip}");
            return [
                'success' => false,
                'error' => 'Kombinasi email dan kata sandi tidak cocok.',
            ];
        }

        // Reset rate limiter on successful authentication
        $this->clearAttempts($throttleKey);

        // Populate permissions
        try {
            $user->permissions = $this->getUserRepo()->getUserPermissions($user->id);
        } catch (\Throwable) {
            $user->permissions = [];
        }

        // Establish session and regenerate ID to prevent session fixation
        $this->login($user);

        Logger::audit((string)$user->id, 'LOGIN', 'users', $user->id, [
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $ip,
        ]);

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    public function login(User $user): void
    {
        Session::start();
        Session::regenerate(true);

        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->permissions,
        ]);
    }

    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            Logger::audit((string)$user['id'], 'LOGOUT', 'users', $user['id']);
        }

        Session::remove('user');
        Session::destroy();
    }

    public function user(): ?array
    {
        return Session::get('user');
    }

    public function check(): bool
    {
        return Session::has('user') && !empty(Session::get('user')['id']);
    }

    public function getThrottleKey(string $email, string $ip): string
    {
        return 'login_attempt_' . md5(strtolower(trim($email)) . '|' . $ip);
    }

    public function isTooManyAttempts(string $key): bool
    {
        $data = Session::get($key);
        if (!$data || !is_array($data)) {
            return false;
        }

        $attempts = $data['attempts'] ?? 0;
        $expiresAt = $data['expires_at'] ?? 0;

        if (time() > $expiresAt) {
            Session::remove($key);
            return false;
        }

        return $attempts >= self::MAX_ATTEMPTS;
    }

    public function incrementAttempts(string $key): void
    {
        $data = Session::get($key) ?? ['attempts' => 0, 'expires_at' => time() + self::DECAY_SECONDS];
        $data['attempts'] = ($data['attempts'] ?? 0) + 1;
        Session::set($key, $data);
    }

    public function getTimeRemaining(string $key): int
    {
        $data = Session::get($key);
        $expiresAt = $data['expires_at'] ?? (time() + self::DECAY_SECONDS);
        return max(1, $expiresAt - time());
    }

    public function clearAttempts(string $key): void
    {
        Session::remove($key);
    }
}
