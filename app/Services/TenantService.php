<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Repositories\TenantRepository;
use App\Repositories\UserRepository;
use RuntimeException;

class TenantService
{
    private TenantRepository $tenantRepo;
    private UserRepository $userRepo;

    public function __construct(?TenantRepository $tenantRepo = null, ?UserRepository $userRepo = null)
    {
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function getAllTenants(): array
    {
        return $this->tenantRepo->getAll();
    }

    public function getTenantById(int $id): ?object
    {
        return $this->tenantRepo->findById($id);
    }

    public function createTenant(array $data, ?string $actor = 'Admin'): array
    {
        $email = strtolower(trim($data['email']));

        // Check if email already registered
        $existingUser = $this->userRepo->findByEmail($email);
        if ($existingUser) {
            throw new RuntimeException("Alamat email {$email} sudah terdaftar di sistem.");
        }

        $userData = [
            'name' => $data['name'],
            'email' => $email,
            'phone' => $data['phone'] ?? null,
        ];

        $tenantData = [
            'id_card_number' => $data['id_card_number'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];

        $result = $this->tenantRepo->createTenant($userData, $tenantData);

        $activationUrl = url('activate?token=' . $result['raw_token']);
        $result['activation_url'] = $activationUrl;

        Logger::audit((string)$actor, 'CREATE_TENANT', 'tenants', $result['tenant_id'], [
            'email' => $email,
            'user_id' => $result['user_id'],
        ]);

        return $result;
    }

    public function activateTenant(string $rawToken, string $newPassword): bool
    {
        $tokenRecord = $this->tenantRepo->verifyActivationToken($rawToken);
        if (!$tokenRecord) {
            throw new RuntimeException("Tautan aktivasi tidak valid, telah digunakan, atau sudah kedaluwarsa.");
        }

        $this->tenantRepo->activateAccount(
            (int)$tokenRecord['id'],
            (int)$tokenRecord['user_id'],
            $newPassword
        );

        Logger::audit((string)$tokenRecord['user_id'], 'ACTIVATE_ACCOUNT', 'users', $tokenRecord['user_id']);

        return true;
    }

    public function updateTenant(int $id, array $data, ?string $actor = 'Admin'): bool
    {
        $tenant = $this->tenantRepo->findById($id);
        if (!$tenant) {
            throw new RuntimeException("Data penghuni tidak ditemukan.");
        }

        $email = strtolower(trim($data['email']));
        $existingUser = $this->userRepo->findByEmail($email);
        if ($existingUser && (int)$existingUser->id !== (int)$tenant->user_id) {
            throw new RuntimeException("Alamat email {$email} sudah digunakan oleh akun lain.");
        }

        $userData = [
            'name' => $data['name'],
            'email' => $email,
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];

        if (!empty($data['password'])) {
            $userData['password'] = (string)$data['password'];
        }

        $tenantData = [
            'id_card_number' => $data['id_card_number'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];

        $success = $this->tenantRepo->updateTenant($id, $userData, $tenantData);

        Logger::audit((string)$actor, 'UPDATE_TENANT', 'tenants', $id, [
            'name' => $data['name'],
            'email' => $email,
            'status' => $data['status'] ?? 'active',
            'password_changed' => !empty($data['password']),
        ]);

        return $success;
    }

    public function resetPassword(int $id, string $newPassword, ?string $actor = 'Admin'): bool
    {
        $tenant = $this->tenantRepo->findById($id);
        if (!$tenant) {
            throw new RuntimeException("Data penghuni tidak ditemukan.");
        }

        if (strlen($newPassword) < 6) {
            throw new RuntimeException("Kata sandi baru minimal 6 karakter.");
        }

        $success = $this->tenantRepo->resetTenantPassword($id, $newPassword);

        Logger::audit((string)$actor, 'RESET_TENANT_PASSWORD', 'tenants', $id, [
            'tenant_name' => $tenant->name,
            'tenant_email' => $tenant->email,
        ]);

        return $success;
    }

    public function deleteTenant(int $id, ?string $actor = 'Admin'): bool
    {
        $tenant = $this->tenantRepo->findById($id);
        if (!$tenant) {
            throw new RuntimeException("Data penghuni tidak ditemukan.");
        }

        if ($this->tenantRepo->hasActiveContractsOrInvoices($id)) {
            throw new RuntimeException("Tidak dapat menghapus penghuni karena memiliki riwayat kontrak aktif atau tagihan belum lunas. Ubah statusnya menjadi 'Nonaktif' melalui menu Ubah.");
        }

        $success = $this->tenantRepo->deleteTenant($id);

        Logger::audit((string)$actor, 'DELETE_TENANT', 'tenants', $id, [
            'name' => $tenant->name,
            'email' => $tenant->email,
        ]);

        return $success;
    }
}

