<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class ExportService
{
    private PDO $db;
    private FinancialReportService $financialService;

    public function __construct(?PDO $db = null, ?FinancialReportService $financialService = null)
    {
        $this->db = $db ?? Database::connect();
        $this->financialService = $financialService ?? new FinancialReportService($this->db);
    }

    /**
     * Export Financial Report (Summary, Verified Incomes, Operational Expenses) as CSV string.
     */
    public function exportFinancialCsv(string $dateFrom, string $dateTo): string
    {
        $summary = $this->financialService->getSummary($dateFrom, $dateTo);

        // Fetch verified payments
        $stmtPay = $this->db->prepare("
            SELECT p.id, p.payment_number, p.payment_date, p.payment_method, p.amount,
                   p.notes, u.name AS tenant_name,
                   (
                       SELECT r.room_number
                       FROM `payment_allocations` pa
                       JOIN `invoices` i ON i.id = pa.invoice_id
                       JOIN `rental_contracts` rc ON rc.id = i.contract_id
                       JOIN `rooms` r ON r.id = rc.room_id
                       WHERE pa.payment_id = p.id
                       LIMIT 1
                   ) AS room_number
            FROM `payments` p
            LEFT JOIN `tenants` t ON t.id = p.tenant_id
            LEFT JOIN `users` u ON u.id = t.user_id
            WHERE p.status = 'verified'
              AND p.payment_date BETWEEN :from AND :to
            ORDER BY p.payment_date ASC, p.id ASC
        ");
        $stmtPay->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $payments = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

        // Fetch expenses
        $stmtExp = $this->db->prepare("
            SELECT e.id, e.transaction_date, c.name AS category_name,
                   e.amount, e.payment_method, e.vendor, e.description
            FROM `expenses` e
            LEFT JOIN `expense_categories` c ON c.id = e.category_id
            WHERE e.deleted_at IS NULL
              AND e.transaction_date BETWEEN :from AND :to
            ORDER BY e.transaction_date ASC, e.id ASC
        ");
        $stmtExp->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $expenses = $stmtExp->fetchAll(PDO::FETCH_ASSOC);

        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Section 1: Header & Financial Summary
        fputcsv($output, ['LAPORAN KEUANGAN KOST'], ',', '"', "\\");
        fputcsv($output, ['Periode', $dateFrom . ' s/d ' . $dateTo], ',', '"', "\\");
        fputcsv($output, ['Tanggal Unduh', date('Y-m-d H:i:s')], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        fputcsv($output, ['RINGKASAN EKSEKUTIF'], ',', '"', "\\");
        fputcsv($output, ['Indikator', 'Nilai (IDR)', 'Keterangan'], ',', '"', "\\");
        fputcsv($output, ['Total Tagihan Diterbitkan', (string)$summary['total_billed'], $summary['invoices_count'] . ' invoice'], ',', '"', "\\");
        fputcsv($output, ['Total Pendapatan Terverifikasi', (string)$summary['total_income'], $summary['verified_payments_count'] . ' transaksi'], ',', '"', "\\");
        fputcsv($output, ['Total Piutang Berjalan', (string)$summary['total_receivables'], 'Sisa tagihan belum lunas'], ',', '"', "\\");
        fputcsv($output, ['Total Pengeluaran Operasional', (string)$summary['total_expense'], $summary['expenses_count'] . ' transaksi'], ',', '"', "\\");
        fputcsv($output, ['Arus Kas Bersih (Net Cash Flow)', (string)$summary['net_cash_flow'], 'Pendapatan - Pengeluaran'], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Section 2: Verified Income Transactions
        fputcsv($output, ['RINCIAN PENDAPATAN (PEMBAYARAN TERVERIFIKASI)'], ',', '"', "\\");
        fputcsv($output, ['No. Transaksi', 'Tanggal', 'Penyewa', 'Kamar', 'Metode', 'Catatan / Referensi', 'Nominal (IDR)'], ',', '"', "\\");
        foreach ($payments as $p) {
            fputcsv($output, [
                $p['payment_number'],
                $p['payment_date'],
                $p['tenant_name'] ?? '-',
                $p['room_number'] ? 'Kamar ' . $p['room_number'] : '-',
                strtoupper($p['payment_method']),
                $p['notes'] ?? '-',
                (string)$p['amount'],
            ], ',', '"', "\\");
        }
        fputcsv($output, [], ',', '"', "\\");

        // Section 3: Operational Expenses
        fputcsv($output, ['RINCIAN PENGELUARAN OPERASIONAL'], ',', '"', "\\");
        fputcsv($output, ['ID', 'Tanggal', 'Kategori', 'Vendor', 'Metode', 'Deskripsi', 'Nominal (IDR)'], ',', '"', "\\");
        foreach ($expenses as $e) {
            fputcsv($output, [
                'EXP-' . str_pad((string)$e['id'], 5, '0', STR_PAD_LEFT),
                $e['transaction_date'],
                $e['category_name'] ?? 'Umum',
                $e['vendor'] ?? '-',
                strtoupper($e['payment_method']),
                $e['description'] ?? '-',
                (string)$e['amount'],
            ], ',', '"', "\\");
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }

    /**
     * Export Operational Report (Rooms status, Active Contracts, Bookings, Maintenance) as CSV string.
     */
    public function exportOperationalCsv(): string
    {
        // 1. Rooms
        $stmtRooms = $this->db->query("
            SELECT r.room_number, r.name AS room_name, r.current_price, r.status,
                   u.name AS tenant_name, rc.start_date, rc.end_date
            FROM `rooms` r
            LEFT JOIN `rental_contracts` rc ON rc.room_id = r.id AND rc.status = 'active'
            LEFT JOIN `tenants` t ON t.id = rc.tenant_id
            LEFT JOIN `users` u ON u.id = t.user_id
            ORDER BY r.room_number ASC
        ");
        $rooms = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);

        // 2. Active Contracts
        $stmtContracts = $this->db->query("
            SELECT rc.contract_number, r.room_number, u.name AS tenant_name, u.phone,
                   rc.start_date, rc.end_date, rc.rent_price_snapshot, rc.billing_anchor_day, rc.status
            FROM `rental_contracts` rc
            JOIN `rooms` r ON r.id = rc.room_id
            JOIN `tenants` t ON t.id = rc.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE rc.status = 'active'
            ORDER BY rc.end_date ASC
        ");
        $contracts = $stmtContracts->fetchAll(PDO::FETCH_ASSOC);

        // 3. Maintenance Requests
        $stmtMaint = $this->db->query("
            SELECT m.id, r.room_number, u.name AS tenant_name, c.name AS category_name,
                   m.title, m.priority, m.status, m.created_at, m.resolved_at
            FROM `maintenance_requests` m
            LEFT JOIN `rooms` r ON r.id = m.room_id
            LEFT JOIN `tenants` t ON t.id = m.tenant_id
            LEFT JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `maintenance_categories` c ON c.id = m.category_id
            ORDER BY m.created_at DESC
        ");
        $maintenance = $stmtMaint->fetchAll(PDO::FETCH_ASSOC);

        $output = fopen('php://temp', 'r+');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['LAPORAN OPERASIONAL KOST'], ',', '"', "\\");
        fputcsv($output, ['Tanggal Unduh', date('Y-m-d H:i:s')], ',', '"', "\\");
        fputcsv($output, [], ',', '"', "\\");

        // Rooms section
        fputcsv($output, ['STATUS DAN INVENTARIS KAMAR'], ',', '"', "\\");
        fputcsv($output, ['Nomor Kamar', 'Nama Kamar', 'Tarif Bulanan (IDR)', 'Status', 'Penghuni Saat Ini', 'Masa Sewa'], ',', '"', "\\");
        foreach ($rooms as $r) {
            $sewa = ($r['start_date'] && $r['end_date']) ? ($r['start_date'] . ' s/d ' . $r['end_date']) : '-';
            fputcsv($output, [
                $r['room_number'],
                $r['room_name'] ?? '-',
                (string)$r['current_price'],
                strtoupper($r['status']),
                $r['tenant_name'] ?? '-',
                $sewa,
            ], ',', '"', "\\");
        }
        fputcsv($output, [], ',', '"', "\\");

        // Contracts section
        fputcsv($output, ['DAFTAR KONTRAK SEWA AKTIF'], ',', '"', "\\");
        fputcsv($output, ['No. Kontrak', 'Kamar', 'Penghuni', 'No. WhatsApp', 'Mulai', 'Selesai', 'Tarif / Bulan', 'Anchor Day'], ',', '"', "\\");
        foreach ($contracts as $c) {
            fputcsv($output, [
                $c['contract_number'],
                'Kamar ' . $c['room_number'],
                $c['tenant_name'],
                $c['phone'],
                $c['start_date'],
                $c['end_date'],
                (string)$c['rent_price_snapshot'],
                'Tgl ' . $c['billing_anchor_day'],
            ], ',', '"', "\\");
        }
        fputcsv($output, [], ',', '"', "\\");

        // Maintenance section
        fputcsv($output, ['DAFTAR TIKET PEMELIHARAAN (MAINTENANCE)'], ',', '"', "\\");
        fputcsv($output, ['No. Tiket', 'Kamar', 'Pelapor', 'Kategori', 'Judul Kerusakan', 'Prioritas', 'Status', 'Tanggal Lapor', 'Tanggal Selesai'], ',', '"', "\\");
        foreach ($maintenance as $m) {
            fputcsv($output, [
                'MNT-' . str_pad((string)$m['id'], 5, '0', STR_PAD_LEFT),
                $m['room_number'] ? 'Kamar ' . $m['room_number'] : '-',
                $m['tenant_name'] ?? '-',
                $m['category_name'] ?? 'Umum',
                $m['title'],
                strtoupper($m['priority']),
                strtoupper($m['status']),
                $m['created_at'],
                $m['resolved_at'] ?? '-',
            ], ',', '"', "\\");
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }
}
