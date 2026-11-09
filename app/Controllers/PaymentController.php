<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\TenantRepository;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;
    private PaymentRepository $paymentRepo;
    private InvoiceRepository $invoiceRepo;
    private TenantRepository $tenantRepo;

    public function __construct(
        ?PaymentService $paymentService = null,
        ?PaymentRepository $paymentRepo = null,
        ?InvoiceRepository $invoiceRepo = null,
        ?TenantRepository $tenantRepo = null
    ) {
        $this->paymentService = $paymentService ?? new PaymentService();
        $this->paymentRepo = $paymentRepo ?? new PaymentRepository();
        $this->invoiceRepo = $invoiceRepo ?? new InvoiceRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
    }

    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }

        $payments = $this->paymentService->getAllPayments($filters);

        return $this->render('admin/payments/index', [
            'title' => 'Daftar Pembayaran',
            'payments' => $payments,
            'currentStatus' => $status,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->param('id');
        $payment = $this->paymentService->getPaymentById($id);

        if (!$payment) {
            Session::flash('error', 'Data pembayaran tidak ditemukan.');
            return $this->redirect('/admin/payments');
        }

        // Get unpaid invoices of this tenant for allocation modal/form
        $tenantInvoices = $this->invoiceRepo->getByTenantId($payment->tenant_id);
        $unpaidInvoices = array_filter($tenantInvoices, fn($i) => in_array($i->status, ['unpaid', 'partially_paid', 'overdue'], true));
        $adjustments = $this->paymentRepo->getAdjustments($id);

        return $this->render('admin/payments/show', [
            'title' => "Pembayaran #{$payment->payment_number}",
            'payment' => $payment,
            'unpaidInvoices' => $unpaidInvoices,
            'adjustments' => $adjustments,
        ]);
    }

    public function recordCash(): Response
    {
        $tenants = $this->tenantRepo->getAll();

        return $this->render('admin/payments/cash', [
            'title' => 'Catat Pembayaran Tunai (Cash)',
            'tenants' => $tenants,
        ]);
    }

    public function storeCash(Request $request): Response
    {
        $validated = $this->validate($request, [
            'tenant_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'payment_date' => 'required',
        ]);

        $user = Session::get('user');

        try {
            $paymentId = $this->paymentService->recordCashPayment([
                'tenant_id' => (int)$validated['tenant_id'],
                'amount' => (float)$validated['amount'],
                'payment_date' => (string)$validated['payment_date'],
                'invoice_id' => $request->post('invoice_id'),
                'notes' => (string)$request->post('notes', 'Pembayaran tunai kasir'),
            ], $user['id'] ?? null, $user['name'] ?? 'Admin');

            Session::flash('success', 'Pembayaran tunai berhasil dicatat dan terverifikasi.');
            return $this->redirect("/admin/payments/{$paymentId}");
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/admin/payments/cash');
        }
    }

    public function verify(Request $request): Response
    {
        $id = (int)$request->param('id');
        $invoiceIds = (array)$request->post('invoice_ids', []);
        $amounts = (array)$request->post('allocated_amounts', []);

        $allocations = [];
        for ($i = 0; $i < count($invoiceIds); $i++) {
            $invId = (int)($invoiceIds[$i] ?? 0);
            $amt = (float)($amounts[$i] ?? 0);
            if ($invId > 0 && $amt > 0) {
                $allocations[] = [
                    'invoice_id' => $invId,
                    'amount' => $amt,
                ];
            }
        }

        $user = Session::get('user');

        try {
            $this->paymentService->verifyPayment($id, $allocations, (int)$user['id'], $user['name'] ?? 'Admin');
            Session::flash('success', 'Pembayaran berhasil diverifikasi dan dialokasikan ke tagihan.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/payments/{$id}");
    }

    public function reject(Request $request): Response
    {
        $id = (int)$request->param('id');
        $reason = (string)$request->post('rejection_reason', '');
        $user = Session::get('user');

        try {
            $this->paymentService->rejectPayment($id, $reason, (int)$user['id'], $user['name'] ?? 'Admin');
            Session::flash('success', 'Pembayaran ditolak.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/payments/{$id}");
    }

    public function reverse(Request $request): Response
    {
        $id = (int)$request->param('id');
        $reason = (string)$request->post('reason', '');
        $user = Session::get('user');

        try {
            $this->paymentService->reversePayment($id, $reason, (int)$user['id'], $user['name'] ?? 'Admin');
            Session::flash('success', 'Pembayaran berhasil dibalikkan (reversed) dan saldo tagihan dikembalikan.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/payments/{$id}");
    }

    public function downloadProof(Request $request): Response
    {
        $id = (int)$request->param('id');
        $payment = $this->paymentService->getPaymentById($id);

        if (!$payment) {
            return new Response('File tidak ditemukan', 404);
        }

        $user = Session::get('user');
        // Authorization: Admin/Owner or owner of the payment
        if ($user['role'] === 'tenant') {
            $tenant = $this->tenantRepo->findByUserId((int)$user['id']);
            if (!$tenant || $tenant->id !== $payment->tenant_id) {
                return new Response('Akses ditolak', 403);
            }
        }

        $proof = $this->paymentRepo->getProofByPaymentId($id);
        if (!$proof) {
            return new Response('Bukti transfer tidak tersedia', 404);
        }

        $fullPath = dirname(__DIR__, 2) . '/storage/uploads/' . $proof['file_path'];
        if (!file_exists($fullPath)) {
            return new Response('File fisik tidak ditemukan pada server', 404);
        }

        header('Content-Type: ' . $proof['mime_type']);
        header('Content-Length: ' . $proof['file_size']);
        header('Content-Disposition: inline; filename="' . $proof['file_name'] . '"');
        readfile($fullPath);
        exit;
    }
}

