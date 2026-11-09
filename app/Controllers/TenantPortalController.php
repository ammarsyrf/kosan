<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\ContractRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\TenantRepository;
use App\Services\ContractService;
use App\Services\InvoiceService;
use App\Services\PaymentService;

class TenantPortalController extends Controller
{
    private ContractService $contractService;
    private ContractRepository $contractRepo;
    private TenantRepository $tenantRepo;
    private InvoiceService $invoiceService;
    private PaymentService $paymentService;

    public function __construct(
        ?ContractService $contractService = null,
        ?ContractRepository $contractRepo = null,
        ?TenantRepository $tenantRepo = null,
        ?InvoiceService $invoiceService = null,
        ?PaymentService $paymentService = null
    ) {
        $this->contractService = $contractService ?? new ContractService();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
        $this->invoiceService = $invoiceService ?? new InvoiceService();
        $this->paymentService = $paymentService ?? new PaymentService();
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
                'invoices' => [],
                'payments' => [],
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

        $invoices = $this->invoiceService->getTenantInvoices($tenant->id);
        $payments = $this->paymentService->getTenantPayments($tenant->id);

        return $this->render('tenant/dashboard', [
            'title' => 'Portal Penyewa',
            'tenant' => $tenant,
            'activeContract' => $activeContract,
            'pastContracts' => $pastContracts,
            'pendingExtension' => $pendingExtension,
            'pendingCheckout' => $pendingCheckout,
            'invoices' => $invoices,
            'payments' => $payments,
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

    public function paymentForm(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        if (!$tenant) {
            Session::flash('error', 'Profil penyewa tidak valid.');
            return $this->redirect('/tenant/dashboard');
        }

        $allInvoices = $this->invoiceService->getTenantInvoices($tenant->id);
        $unpaidInvoices = array_filter($allInvoices, fn($i) => in_array($i->status, ['unpaid', 'partially_paid', 'overdue'], true));

        return $this->render('tenant/payments', [
            'title' => 'Kirim Bukti Pembayaran',
            'invoices' => $unpaidInvoices,
        ]);
    }

    public function submitPayment(Request $request): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        if (!$tenant) {
            Session::flash('error', 'Profil penyewa tidak valid.');
            return $this->redirect('/tenant/dashboard');
        }

        $validated = $this->validate($request, [
            'amount' => 'required|numeric',
            'payment_date' => 'required',
        ]);

        $file = $_FILES['proof'] ?? null;

        try {
            $paymentId = $this->paymentService->submitTenantPayment([
                'tenant_id' => $tenant->id,
                'amount' => (float)$validated['amount'],
                'payment_date' => (string)$validated['payment_date'],
                'payment_method' => 'transfer',
                'invoice_id' => $request->post('invoice_id'),
                'notes' => (string)$request->post('notes', ''),
            ], $file, $user['name'] ?? 'Tenant');

            Session::flash('success', 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi pengelola kos.');
            return $this->redirect('/tenant/dashboard');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/tenant/payment');
        }
    }
}
