<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ExportService;

class ExportController extends Controller
{
    private ExportService $exportService;

    public function __construct(?ExportService $exportService = null)
    {
        $this->exportService = $exportService ?? new ExportService();
    }

    /**
     * Export Financial Report (CSV). Allowed for Owner only.
     */
    public function exportFinancial(Request $request): Response
    {
        $user = Session::get('user');
        if (strtolower($user['role'] ?? '') !== 'owner') {
            Session::flash('error', 'Akses ditolak: Hanya Owner yang dapat mengunduh laporan keuangan.');
            return $this->redirect('/admin/dashboard');
        }

        $dateFrom = $request->query('date_from', date('Y-m-01'));
        $dateTo = $request->query('date_to', date('Y-m-t'));

        $csv = $this->exportService->exportFinancialCsv($dateFrom, $dateTo);
        $filename = 'Laporan_Keuangan_Kost_' . $dateFrom . '_sd_' . $dateTo . '.csv';

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Operational Report (CSV). Allowed for Owner and Admin.
     */
    public function exportOperational(Request $request): Response
    {
        $user = Session::get('user');
        $role = strtolower($user['role'] ?? '');
        if (!in_array($role, ['owner', 'admin'], true)) {
            Session::flash('error', 'Akses ditolak.');
            return $this->redirect('/login');
        }

        $csv = $this->exportService->exportOperationalCsv();
        $filename = 'Laporan_Operasional_Kost_' . date('Ymd_His') . '.csv';

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
