<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\ContractRepository;
use App\Repositories\TenantRepository;
use App\Services\ContractService;

class TenantPortalController extends Controller
{
    private ContractService $contractService;
    private ContractRepository $contractRepo;
    private TenantRepository $tenantRepo;

    public function __construct(
        ?ContractService $contractService = null,
        ?ContractRepository $contractRepo = null,
        ?TenantRepository $tenantRepo = null
    ) {
        $this->contractService = $contractService ?? new ContractService();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
    }

    public function dashboard(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);

        if (!$tenant) {
            Session::flash('error', 'Profil penyewa belum terhubung ke akun Anda.');
            return $this->render('tenant/dashboard', [
                'title' => 'Portal Penyewa',
                'tenant' => null,
                'activeContract' => null,
                'pastContracts' => [],
                'pendingExtension' => null,
                'pendingCheckout' => null,
            ]);
        }

        $contracts = $this->contractService->getTenantContracts($tenant->id);
        $activeContract = null;
        $pastContracts = [];

        foreach ($contracts as $c) {
            if ($c->isActive()) {
                $activeContract = $c;
            } else {
                $pastContracts[] = $c;
            }
        }

        $pendingExtension = null;
        $pendingCheckout = null;

        if ($activeContract) {
            $pendingExtension = $this->contractRepo->getPendingExtensionByContractId($activeContract->id);
            $pendingCheckout = $this->contractRepo->getPendingEarlyCheckoutByContractId($activeContract->id);
        }

        return $this->render('tenant/dashboard', [
            'title' => 'Portal Penyewa',
            'tenant' => $tenant,
            'activeContract' => $activeContract,
            'pastContracts' => $pastContracts,
            'pendingExtension' => $pendingExtension,
            'pendingCheckout' => $pendingCheckout,
        ]);
    }

    public function extensionForm(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Anda tidak memiliki kontrak sewa aktif.');
            return $this->redirect('/tenant/dashboard');
        }

        return $this->render('tenant/extension', [
            'title' => 'Ajukan Perpanjangan Sewa',
            'contract' => $activeContract,
        ]);
    }

    public function submitExtension(Request $request): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Kontrak aktif tidak ditemukan.');
            return $this->redirect('/tenant/dashboard');
        }

        $months = (int)$request->post('months', 1);
        if ($months < 1) {
            Session::flash('error', 'Durasi perpanjangan minimal 1 bulan.');
            return $this->redirect('/tenant/extension');
        }

        try {
            $this->contractService->requestExtension($activeContract->id, $months, $user['name'] ?? 'Tenant');
            Session::flash('success', 'Pengajuan perpanjangan sewa berhasil dikirim. Menunggu verifikasi admin.');
            return $this->redirect('/tenant/dashboard');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/tenant/extension');
        }
    }

    public function checkoutForm(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Anda tidak memiliki kontrak sewa aktif.');
            return $this->redirect('/tenant/dashboard');
        }

        return $this->render('tenant/checkout', [
            'title' => 'Ajukan Early Checkout',
            'contract' => $activeContract,
        ]);
    }

    public function submitCheckout(Request $request): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Kontrak aktif tidak ditemukan.');
            return $this->redirect('/tenant/dashboard');
        }

        $checkoutDate = (string)$request->post('requested_checkout_date', '');
        $reason = (string)$request->post('reason', '');

        if (!$checkoutDate) {
            Session::flash('error', 'Tanggal checkout wajib diisi.');
            return $this->redirect('/tenant/early-checkout');
        }

        try {
            $this->contractService->requestEarlyCheckout($activeContract->id, $checkoutDate, $reason, $user['name'] ?? 'Tenant');
            Session::flash('success', 'Pengajuan early checkout berhasil dikirim. Menunggu keputusan pengelola kos.');
            return $this->redirect('/tenant/dashboard');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/tenant/early-checkout');
        }
    }
}
