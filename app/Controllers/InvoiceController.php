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
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;
    private ContractRepository $contractRepo;

    public function __construct(
        ?InvoiceService $invoiceService = null,
        ?ContractRepository $contractRepo = null
    ) {
        $this->invoiceService = $invoiceService ?? new InvoiceService();
        $this->contractRepo = $contractRepo ?? new ContractRepository();
    }

    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }

        $invoices = $this->invoiceService->getAllInvoices($filters);

        return $this->render('admin/invoices/index', [
            'title' => 'Daftar Tagihan (Invoice)',
            'invoices' => $invoices,
            'currentStatus' => $status,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->param('id');
        $invoice = $this->invoiceService->getInvoiceById($id);

        if (!$invoice) {
            Session::flash('error', 'Invoice tidak ditemukan.');
            return $this->redirect('/admin/invoices');
        }

        return $this->render('admin/invoices/show', [
            'title' => "Invoice #{$invoice->invoice_number}",
            'invoice' => $invoice,
        ]);
    }

    public function create(): Response
    {
        $contracts = $this->contractRepo->getAll();
        $activeContracts = array_filter($contracts, fn($c) => $c->isActive());

        return $this->render('admin/invoices/create', [
            'title' => 'Buat Tagihan Manual',
            'contracts' => $activeContracts,
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'contract_id' => 'required|numeric',
            'period_start' => 'required',
            'period_end' => 'required',
            'issue_date' => 'required',
            'due_date' => 'required',
        ]);

        $descriptions = (array)$request->post('item_description', []);
        $amounts = (array)$request->post('item_amount', []);

        $items = [];
        for ($i = 0; $i < count($descriptions); $i++) {
            $desc = trim((string)($descriptions[$i] ?? ''));
            $amt = (float)($amounts[$i] ?? 0);
            if ($desc !== '' && $amt > 0) {
                $items[] = [
                    'description' => $desc,
                    'amount' => $amt,
                ];
            }
        }

        if (empty($items)) {
            Session::flash('error', 'Masukkan minimal 1 item rincian tagihan dengan nominal lebih dari 0.');
            Session::flash('old', $request->all());
            return $this->redirect('/admin/invoices/create');
        }

        $user = Session::get('user');

        try {
            $invoiceId = $this->invoiceService->createManualInvoice([
                'contract_id' => (int)$validated['contract_id'],
                'period_start' => (string)$validated['period_start'],
                'period_end' => (string)$validated['period_end'],
                'issue_date' => (string)$validated['issue_date'],
                'due_date' => (string)$validated['due_date'],
                'is_first_month_manual' => $request->post('is_first_month_manual') ? 1 : 0,
                'notes' => (string)$request->post('notes', ''),
            ], $items, $user['name'] ?? 'Admin');

            Session::flash('success', 'Tagihan (invoice) berhasil diterbitkan.');
            return $this->redirect("/admin/invoices/{$invoiceId}");
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/invoices/create');
        }
    }

    public function cancel(Request $request): Response
    {
        $id = (int)$request->param('id');
        $reason = (string)$request->post('reason', 'Dibatalkan oleh admin');
        $user = Session::get('user');

        try {
            $this->invoiceService->cancelInvoice($id, $reason, $user['name'] ?? 'Admin');
            Session::flash('success', 'Invoice berhasil dibatalkan.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/invoices/{$id}");
    }
}

