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

    /**
     * Export All / Filtered Invoices List to Excel CSV (UTF-8 BOM)
     */
    public function export(Request $request): Response
    {
        $status = $request->get('status');
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }

        $invoices = $this->invoiceService->getAllInvoices($filters);

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        $bizName = setting('business_name', 'Kost Management System');
        fputcsv($output, ["DAFTAR INVOICE & TAGIHAN - {$bizName}"], ',', '"', "\\");
        fputcsv($output, ['Filter Status', !empty($status) ? strtoupper(str_replace('_', ' ', (string)$status)) : 'SEMUA STATUS'], ',', '"', "\\");
        fputcsv($output, ['Tanggal Unduh', date('Y-m-d H:i:s')], ',', '"', "\\");
        fputcsv($output, ['Total Data', count($invoices) . ' Invoice'], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Table Header
        fputcsv($output, [
            'No.',
            'No. Invoice',
            'No. Kontrak',
            'Nama Penyewa',
            'Kamar',
            'No. Telepon',
            'Periode Sewa Mulai',
            'Periode Sewa Selesai',
            'Tanggal Terbit',
            'Jatuh Tempo',
            'Total Tagihan (Rp)',
            'Telah Dibayar (Rp)',
            'Sisa Tagihan (Rp)',
            'Status',
            'Catatan'
        ], ',', '"', "\\");

        $no = 1;
        $sumTotal = 0.0;
        $sumPaid = 0.0;
        $sumBalance = 0.0;

        foreach ($invoices as $inv) {
            $sumTotal += $inv->total_amount;
            $sumPaid += $inv->paid_amount;
            $sumBalance += $inv->balance;

            $statusLabel = match ($inv->status) {
                'paid' => 'LUNAS',
                'unpaid' => 'BELUM DIBAYAR',
                'partially_paid' => 'DIBAYAR SEBAGIAN',
                'overdue' => 'JATUH TEMPO',
                'cancelled' => 'DIBATALKAN',
                'void' => 'VOID',
                default => strtoupper($inv->status)
            };

            fputcsv($output, [
                $no++,
                $inv->invoice_number,
                $inv->contract_number ?? '-',
                $inv->tenant_name ?? '-',
                'Kamar ' . ($inv->room_number ?? '-'),
                $inv->tenant_phone ?? '-',
                $inv->period_start,
                $inv->period_end,
                $inv->issue_date,
                $inv->due_date,
                (string)$inv->total_amount,
                (string)$inv->paid_amount,
                (string)$inv->balance,
                $statusLabel,
                $inv->notes ?? '-'
            ], ',', '"', "\\");
        }

        fputcsv($output, [], ',', '"', "\\");
        fputcsv($output, [
            '', '', '', '', '', '', '', '', 'TOTAL',
            (string)$sumTotal,
            (string)$sumPaid,
            (string)$sumBalance,
            '', ''
        ], ',', '"', "\\");

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $filename = 'Daftar_Invoice_Kost_' . date('Ymd_His') . '.csv';

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Single Invoice Details to Excel CSV (UTF-8 BOM)
     */
    public function exportSingle(Request $request): Response
    {
        $id = (int)$request->param('id');
        $invoice = $this->invoiceService->getInvoiceById($id);

        if (!$invoice) {
            Session::flash('error', 'Invoice tidak ditemukan.');
            return $this->redirect('/admin/invoices');
        }

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        $bizName = setting('business_name', 'Kost Management System');
        $bizAddress = setting('business_address', 'Properti Kos');
        $bizPhone = setting('business_phone', '-');

        // Header Section
        fputcsv($output, ["INVOICE RESMI - {$bizName}"], ',', '"', "\\");
        fputcsv($output, ['Alamat Properti', $bizAddress], ',', '"', "\\");
        fputcsv($output, ['Kontak / WhatsApp', $bizPhone], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Invoice Meta
        fputcsv($output, ['INFORMASI TAGIHAN'], ',', '"', "\\");
        fputcsv($output, ['Nomor Invoice', $invoice->invoice_number], ',', '"', "\\");
        fputcsv($output, ['Nomor Kontrak', $invoice->contract_number ?? '-'], ',', '"', "\\");
        fputcsv($output, ['Status Tagihan', strtoupper(str_replace('_', ' ', $invoice->status))], ',', '"', "\\");
        fputcsv($output, ['Tanggal Terbit', $invoice->issue_date], ',', '"', "\\");
        fputcsv($output, ['Jatuh Tempo', $invoice->due_date], ',', '"', "\\");
        fputcsv($output, ['Periode Sewa', $invoice->period_start . ' s/d ' . $invoice->period_end], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Tenant Meta
        fputcsv($output, ['TUJUAN PENAGIHAN'], ',', '"', "\\");
        fputcsv($output, ['Nama Penyewa', $invoice->tenant_name], ',', '"', "\\");
        fputcsv($output, ['Kamar', 'Kamar ' . $invoice->room_number], ',', '"', "\\");
        fputcsv($output, ['No. Telepon', $invoice->tenant_phone ?: '-'], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Invoice Items Table
        fputcsv($output, ['RINCIAN ITEM TAGIHAN'], ',', '"', "\\");
        fputcsv($output, ['No.', 'Deskripsi Item Tagihan', 'Nominal (Rp)'], ',', '"', "\\");
        $no = 1;
        foreach ($invoice->items as $item) {
            fputcsv($output, [
                $no++,
                $item->description,
                (string)$item->amount
            ], ',', '"', "\\");
        }
        fputcsv($output, ['', 'Subtotal Tagihan', (string)$invoice->subtotal], ',', '"', "\\");
        fputcsv($output, ['', 'Total Tagihan', (string)$invoice->total_amount], ',', '"', "\\");
        fputcsv($output, ['', 'Telah Dibayar (Verified)', (string)$invoice->paid_amount], ',', '"', "\\");
        fputcsv($output, ['', 'Sisa Tagihan (Balance)', (string)$invoice->balance], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Payment History
        if (!empty($invoice->payments)) {
            fputcsv($output, ['RIWAYAT PEMBAYARAN'], ',', '"', "\\");
            fputcsv($output, ['No. Transaksi', 'Tanggal Bayar', 'Metode', 'Status', 'Nominal Dialokasikan (Rp)'], ',', '"', "\\");
            foreach ($invoice->payments as $p) {
                fputcsv($output, [
                    $p['payment_number'],
                    $p['payment_date'],
                    strtoupper($p['payment_method']),
                    strtoupper($p['status']),
                    (string)$p['allocated_amount']
                ], ',', '"', "\\");
            }
            fputcsv($output, [], ',', '"', "\\");
        }

        // Bank Instructions
        fputcsv($output, ['INSTRUKSI REKENING PEMBAYARAN'], ',', '"', "\\");
        fputcsv($output, ['Nama Bank', setting('bank_name', 'BCA - Bank Central Asia')], ',', '"', "\\");
        fputcsv($output, ['Nomor Rekening', (string)setting('bank_account_number', '1234567890')], ',', '"', "\\");
        fputcsv($output, ['Atas Nama', setting('bank_account_holder', 'Pengelola Kost')], ',', '"', "\\");

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $safeNum = preg_replace('/[^A-Za-z0-9_-]/', '_', $invoice->invoice_number);
        $filename = "Invoice_{$safeNum}.csv";

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Standalone Clean Printable Invoice View (Auto print dialog)
     */
    public function printInvoice(Request $request): Response
    {
        $id = (int)$request->param('id');
        $invoice = $this->invoiceService->getInvoiceById($id);

        if (!$invoice) {
            Session::flash('error', 'Invoice tidak ditemukan.');
            return $this->redirect('/admin/invoices');
        }

        return $this->render('admin/invoices/print', [
            'title' => "Cetak Invoice #{$invoice->invoice_number}",
            'invoice' => $invoice,
        ], null);
    }
}

