<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\DashboardService;
use App\Services\FinancialReportService;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;
    private FinancialReportService $financialService;

    public function __construct(
        ?DashboardService $dashboardService = null,
        ?FinancialReportService $financialService = null
    ) {
        $this->dashboardService = $dashboardService ?? new DashboardService();
        $this->financialService = $financialService ?? new FinancialReportService();
    }

    /**
     * Owner Dashboard: Complete financial performance, comparisons, operations, alerts.
     */
    public function owner(Request $request): Response
    {
        $user = Session::get('user');
        if (strtolower($user['role'] ?? '') !== 'owner') {
            return $this->redirect('/admin/dashboard');
        }

        // Period filter options
        $filter = $request->query('filter', 'this_month');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($filter === 'today') {
            $dateFrom = date('Y-m-d');
            $dateTo = date('Y-m-d');
        } elseif ($filter === 'last_month') {
            $dateFrom = date('Y-m-01', strtotime('first day of last month'));
            $dateTo = date('Y-m-t', strtotime('last day of last month'));
        } elseif ($filter === 'this_year') {
            $dateFrom = date('Y-01-01');
            $dateTo = date('Y-12-31');
        } elseif ($filter === 'custom' && $dateFrom && $dateTo) {
            // keep custom dates
        } else {
            // default this_month
            $filter = 'this_month';
            $dateFrom = date('Y-m-01');
            $dateTo = date('Y-m-t');
        }

        $data = $this->dashboardService->getOwnerDashboardData($dateFrom, $dateTo);

        return $this->render('admin/dashboard/owner', [
            'title' => 'Executive Owner Dashboard — Kost Management',
            'user' => $user,
            'filter' => $filter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'financial' => $data['financial'],
            'comparison' => $data['comparison'],
            'operations' => $data['operations'],
            'alerts' => $data['alerts'],
            'recent_logs' => $data['recent_logs'],
            'revenue_per_room' => $data['revenue_per_room'],
            'monthly_trend' => $data['monthly_trend'],
        ]);
    }

    /**
     * Admin Dashboard: Operational daily management, actionable queue, operational alerts.
     */
    public function admin(Request $request): Response
    {
        $user = Session::get('user');
        $role = strtolower($user['role'] ?? '');

        // If owner visits /admin/dashboard without specifying, they can see owner dashboard or admin dashboard
        // If query param 'view=operational' is not passed and role is owner, redirect to owner dashboard
        if ($role === 'owner' && $request->query('view') !== 'operational') {
            return $this->redirect('/owner/dashboard');
        }

        $data = $this->dashboardService->getAdminDashboardData();

        return $this->render('admin/dashboard/admin', [
            'title' => 'Admin Operations Dashboard — Kost Management',
            'user' => $user,
            'operations' => $data['operations'],
            'alerts' => $data['alerts'],
            'recent_logs' => $data['recent_logs'],
            'pending_bookings' => $data['pending_bookings'],
            'pending_payments' => $data['pending_payments'],
            'urgent_maintenance' => $data['urgent_maintenance'],
            'expiring_contracts' => $data['expiring_contracts'],
        ]);
    }

    /**
     * Financial Reports View (Summary & Analytics).
     */
    public function financialReport(Request $request): Response
    {
        $user = Session::get('user');
        if (strtolower($user['role'] ?? '') !== 'owner') {
            Session::flash('error', 'Akses laporan keuangan hanya untuk Owner.');
            return $this->redirect('/admin/dashboard');
        }

        $dateFrom = $request->query('date_from', date('Y-m-01'));
        $dateTo = $request->query('date_to', date('Y-m-t'));

        $summary = $this->financialService->getSummary($dateFrom, $dateTo);

        return $this->render('admin/reports/financial', [
            'title' => 'Laporan Keuangan & Arus Kas',
            'user' => $user,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'summary' => $summary,
        ]);
    }
}
