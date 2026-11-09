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
use App\Services\MaintenanceService;
use App\Services\PaymentService;

class TenantPortalController extends Controller
{
    private ContractService $contractService;
    private ContractRepository $contractRepo;
    private TenantRepository $tenantRepo;
    private InvoiceService $invoiceService;
    private PaymentService $paymentService;
    private MaintenanceService $maintService;

    public function __construct(
        ?ContractService $contractService = null,
        ?ContractRepository $contractRepo = null,
        ?TenantRepository $tenantRepo = null,
        ?InvoiceService $invoiceService = null,
        ?PaymentService $paymentService = null,
        ?MaintenanceService $maintService = null
    ) {
        $this->contractService = $contractService ?? new ContractService();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
        $this->invoiceService = $invoiceService ?? new InvoiceService();
        $this->paymentService = $paymentService ?? new PaymentService();
        $this->maintService = $maintService ?? new MaintenanceService();
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
        $maintRequests = $this->maintService->getTenantRequests($tenant->id);

        $totalBilled = 0.0;
        $remainingBalance = 0.0;
        foreach ($invoices as $inv) {
            if (!in_array($inv->status, ['cancelled', 'void'], true)) {
                $totalBilled += (float)$inv->total_amount;
                $remainingBalance += (float)$inv->balance;
            }
        }

        $totalPaid = 0.0;
        foreach ($payments as $p) {
            if ($p->status === 'verified') {
                $totalPaid += (float)$p->amount;
            }
        }

        return $this->render('tenant/dashboard', [
            'title' => 'Portal Penyewa',
            'tenant' => $tenant,
            'activeContract' => $activeContract,
            'pastContracts' => $pastContracts,
            'pendingExtension' => $pendingExtension,
            'pendingCheckout' => $pendingCheckout,
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
            'invoices' => $invoices,
            'payments' => $payments,
            'maintenanceRequests' => $maintRequests,
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

    public function earlyCheckoutForm(): Response
    {
        return $this->checkoutForm();
    }

    public function submitEarlyCheckout(Request $request): Response
    {
        return $this->submitCheckout($request);
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

    // --- M6: Tenant Maintenance Management ---
    public function maintenanceIndex(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        if (!$tenant) {
            Session::flash('error', 'Profil penyewa tidak valid.');
            return $this->redirect('/tenant/dashboard');
        }

        $requests = $this->maintService->getTenantRequests($tenant->id);
        $activeContract = $this->contractService->getActiveContractByTenantId($tenant->id);

        return $this->render('tenant/maintenance/index', [
            'title' => 'Laporan Perbaikan Kamar',
            'requests' => $requests,
            'activeContract' => $activeContract,
        ]);
    }

    public function maintenanceCreate(): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Anda harus memiliki kontrak sewa aktif untuk membuat laporan perbaikan.');
            return $this->redirect('/tenant/dashboard');
        }

        $categories = $this->maintService->getAllCategories();

        return $this->render('tenant/maintenance/create', [
            'title' => 'Buat Laporan Perbaikan Baru',
            'contract' => $activeContract,
            'categories' => $categories,
            'allowedPriorities' => MaintenanceService::ALLOWED_PRIORITIES,
        ]);
    }

    public function maintenanceStore(Request $request): Response
    {
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
        $activeContract = $tenant ? $this->contractService->getActiveContractByTenantId($tenant->id) : null;

        if (!$activeContract) {
            Session::flash('error', 'Kontrak sewa aktif tidak ditemukan.');
            return $this->redirect('/tenant/dashboard');
        }

        $title = (string)$request->post('title');
        $description = (string)$request->post('description');
        $categoryId = (int)$request->post('category_id');
        $priority = (string)$request->post('priority', 'medium');

        $files = $_FILES['attachments'] ?? ($_FILES['attachment'] ?? []);

        try {
            $id = $this->maintService->createRequest(
                $tenant->id,
                $activeContract->room_id,
                $categoryId,
                $title,
                $description,
                $priority,
                $files,
                $user['name'] ?? 'Tenant'
            );

            Session::flash('success', 'Laporan perbaikan berhasil diajukan! Pengelola kos akan segera menindaklanjuti.');
            return $this->redirect('/tenant/maintenance/' . $id);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/tenant/maintenance/create');
        }
    }

    public function maintenanceShow(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);

        if (!$tenant) {
            Session::flash('error', 'Profil penyewa tidak valid.');
            return $this->redirect('/tenant/dashboard');
        }

        // IncludeInternal = false (strictly hides internal admin notes from tenant)
        $maintRequest = $this->maintService->getRequestById($id, false);

        if (!$maintRequest) {
            Session::flash('error', 'Laporan perbaikan tidak ditemukan.');
            return $this->redirect('/tenant/maintenance');
        }

        // Strict IDOR Protection: Tenant can ONLY view their own maintenance requests
        if ($maintRequest->tenant_id !== $tenant->id) {
            return new Response('Akses ditolak: Anda tidak memiliki akses ke laporan ini.', 403);
        }

        return $this->render('tenant/maintenance/show', [
            'title' => 'Laporan Perbaikan #' . $maintRequest->id,
            'req' => $maintRequest,
        ]);
    }

    public function maintenanceAddUpdate(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);

        if (!$tenant) {
            return new Response('Unauthorized', 401);
        }

        $maintRequest = $this->maintService->getRequestById($id, false);
        if (!$maintRequest) {
            Session::flash('error', 'Laporan perbaikan tidak ditemukan.');
            return $this->redirect('/tenant/maintenance');
        }

        // Strict IDOR check
        if ($maintRequest->tenant_id !== $tenant->id) {
            return new Response('Akses ditolak.', 403);
        }

        $notes = trim((string)$request->post('notes'));
        if (empty($notes)) {
            Session::flash('error', 'Keterangan tambahan tidak boleh kosong.');
            return $this->redirect('/tenant/maintenance/' . $id);
        }

        try {
            // Tenant updates are always public (is_internal = false)
            $this->maintService->addUpdate($id, (int)$user['id'], $notes, false, $user['name'] ?? 'Tenant');
            Session::flash('success', 'Keterangan tambahan berhasil dikirim.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/tenant/maintenance/' . $id);
    }

    public function downloadMaintenanceAttachment(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $tenant = $this->tenantRepo->findByUserId((int)$user['id']);

        if (!$tenant) {
            return new Response('Akses ditolak', 403);
        }

        $attachment = $this->maintService->getAttachmentById($id);
        if (!$attachment) {
            return new Response('File lampiran tidak ditemukan.', 404);
        }

        $maintRequest = $this->maintService->getRequestById($attachment->request_id, false);
        if (!$maintRequest || $maintRequest->tenant_id !== $tenant->id) {
            return new Response('Akses ditolak: File ini bukan milik laporan Anda.', 403);
        }

        $fullPath = dirname(__DIR__, 2) . '/storage/uploads/' . $attachment->file_path;
        if (!file_exists($fullPath)) {
            return new Response('File fisik tidak ditemukan di storage server.', 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fullPath);
        if (PHP_VERSION_ID < 80500) {
            finfo_close($finfo);
        }

        $content = file_get_contents($fullPath);

        return new Response($content, 200, [
            'Content-Type' => $mime ?: $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"',
            'Content-Length' => (string)filesize($fullPath),
        ]);
    }
}
