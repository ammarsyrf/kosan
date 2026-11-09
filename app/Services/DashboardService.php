<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\BookingRepository;
use App\Repositories\ContractRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RoomRepository;
use PDO;

class DashboardService
{
    private PDO $db;
    private FinancialReportService $financialService;
    private RoomRepository $roomRepo;
    private BookingRepository $bookingRepo;
    private ContractRepository $contractRepo;
    private MaintenanceRepository $maintenanceRepo;
    private NotificationRepository $notifRepo;

    public function __construct(
        ?PDO $db = null,
        ?FinancialReportService $financialService = null,
        ?RoomRepository $roomRepo = null,
        ?BookingRepository $bookingRepo = null,
        ?ContractRepository $contractRepo = null,
        ?MaintenanceRepository $maintenanceRepo = null,
        ?NotificationRepository $notifRepo = null
    ) {
        $this->db = $db ?? Database::connect();
        $this->financialService = $financialService ?? new FinancialReportService($this->db);
        $this->roomRepo = $roomRepo ?? new RoomRepository($this->db);
        $this->bookingRepo = $bookingRepo ?? new BookingRepository($this->db);
        $this->contractRepo = $contractRepo ?? new ContractRepository($this->db);
        $this->maintenanceRepo = $maintenanceRepo ?? new MaintenanceRepository($this->db);
        $this->notifRepo = $notifRepo ?? new NotificationRepository($this->db);
    }

    /**
     * Get complete dashboard data for Owner.
     */
    public function getOwnerDashboardData(?string $dateFrom = null, ?string $dateTo = null): array
    {
        // 1. Current Period Financial Summary
        $dateFrom = $dateFrom ?? date('Y-m-01');
        $dateTo = $dateTo ?? date('Y-m-t');

        $currentFinancial = $this->financialService->getSummary($dateFrom, $dateTo);

        // 2. Previous Period Financial Summary (for comparison)
        $diffDays = (int)ceil((strtotime($dateTo) - strtotime($dateFrom)) / 86400) + 1;
        $prevDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        $prevDateFrom = date('Y-m-d', strtotime($prevDateTo . ' -' . ($diffDays - 1) . ' days'));
        $previousFinancial = $this->financialService->getSummary($prevDateFrom, $prevDateTo);

        // Calculate comparison trends (percentages)
        $comparison = [
            'income_change_pct' => $this->calculatePercentageChange($previousFinancial['total_income'], $currentFinancial['total_income']),
            'expense_change_pct' => $this->calculatePercentageChange($previousFinancial['total_expense'], $currentFinancial['total_expense']),
            'net_change_pct' => $this->calculatePercentageChange($previousFinancial['net_cash_flow'], $currentFinancial['net_cash_flow']),
            'prev_period' => [
                'from' => $prevDateFrom,
                'to' => $prevDateTo,
            ],
            'prev_income' => $previousFinancial['total_income'],
            'prev_expense' => $previousFinancial['total_expense'],
            'prev_net_cash_flow' => $previousFinancial['net_cash_flow'],
        ];

        // 3. Operational Metrics
        $operations = $this->getOperationalMetrics();

        // 4. Alerts & Action Items
        $alerts = $this->getAlertsAndActionItems();

        // 5. Recent Activity Logs (Audit Trail)
        $recentLogs = $this->getRecentActivityLogs(10);

        // 6. Revenue Per Room Breakdown (for current period)
        $revenuePerRoom = $this->getRevenuePerRoom($dateFrom, $dateTo);

        // 7. Monthly Trend (Last 6 Months) for Charts
        $monthlyTrend = $this->getMonthlyTrend(6);

        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'financial' => $currentFinancial,
            'comparison' => $comparison,
            'operations' => $operations,
            'alerts' => $alerts,
            'recent_logs' => $recentLogs,
            'revenue_per_room' => $revenuePerRoom,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Get operational dashboard data for Admin (Hides sensitive owner net profit data).
     */
    public function getAdminDashboardData(): array
    {
        $operations = $this->getOperationalMetrics();
        $alerts = $this->getAlertsAndActionItems();
        $recentLogs = $this->getRecentActivityLogs(10);

        // Pending items specifically needing Admin attention
        $stmtBookings = $this->db->prepare("
            SELECT b.*, r.room_number, u.name AS tenant_name
            FROM `bookings` b
            JOIN `rooms` r ON r.id = b.room_id
            JOIN `tenants` t ON t.id = b.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE b.status = 'pending'
            ORDER BY b.created_at ASC
            LIMIT 5
        ");
        $stmtBookings->execute();
        $pendingBookings = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

        $pendingPayments = $this->getPendingPaymentsList(5);
        $urgentMaintenance = $this->getUrgentMaintenanceList(5);
        $expiringContracts = $this->getExpiringContractsList(30, 5);

        return [
            'operations' => $operations,
            'alerts' => $alerts,
            'recent_logs' => $recentLogs,
            'pending_bookings' => $pendingBookings,
            'pending_payments' => $pendingPayments,
            'urgent_maintenance' => $urgentMaintenance,
            'expiring_contracts' => $expiringContracts,
        ];
    }

    /**
     * Get tenant dashboard data with strict tenant isolation.
     */
    public function getTenantDashboardData(int $tenantId): array
    {
        // 1. Tenant & Active Contract
        $contracts = $this->contractRepo->getByTenantId($tenantId);
        $activeContract = null;
        $pastContracts = [];

        foreach ($contracts as $c) {
            if ($c->isActive()) {
                $activeContract = $c;
            } else {
                $pastContracts[] = $c;
            }
        }

        // 2. Pending Requests
        $pendingExtension = null;
        $pendingCheckout = null;
        if ($activeContract) {
            $stmtExt = $this->db->prepare("
                SELECT * FROM `contract_extension_requests`
                WHERE `contract_id` = :cid AND `status` = 'pending'
                ORDER BY `created_at` DESC LIMIT 1
            ");
            $stmtExt->execute(['cid' => $activeContract->id]);
            $pendingExtension = $stmtExt->fetch(PDO::FETCH_ASSOC) ?: null;

            $stmtChk = $this->db->prepare("
                SELECT * FROM `early_checkout_requests`
                WHERE `contract_id` = :cid AND `status` = 'pending'
                ORDER BY `created_at` DESC LIMIT 1
            ");
            $stmtChk->execute(['cid' => $activeContract->id]);
            $pendingCheckout = $stmtChk->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        // 3. Billing & Payments Summary for this Tenant
        $stmtInv = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) AS total_billed,
                   COALESCE(SUM(balance), 0) AS remaining_balance
            FROM `invoices`
            WHERE `tenant_id` = :tenant_id
              AND `status` NOT IN ('cancelled', 'void')
        ");
        $stmtInv->execute(['tenant_id' => $tenantId]);
        $invSummary = $stmtInv->fetch(PDO::FETCH_ASSOC);

        $stmtPay = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) AS total_paid
            FROM `payments`
            WHERE `tenant_id` = :tenant_id
              AND `status` = 'verified'
        ");
        $stmtPay->execute(['tenant_id' => $tenantId]);
        $totalPaid = (float)$stmtPay->fetchColumn();

        // 4. Invoices and Payments Lists
        $stmtInvoices = $this->db->prepare("
            SELECT * FROM `invoices`
            WHERE `tenant_id` = :tenant_id
            ORDER BY `issue_date` DESC
            LIMIT 10
        ");
        $stmtInvoices->execute(['tenant_id' => $tenantId]);
        $invoices = $stmtInvoices->fetchAll(PDO::FETCH_ASSOC);

        $stmtPayments = $this->db->prepare("
            SELECT * FROM `payments`
            WHERE `tenant_id` = :tenant_id
            ORDER BY `created_at` DESC
            LIMIT 10
        ");
        $stmtPayments->execute(['tenant_id' => $tenantId]);
        $payments = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);

        // 5. Active Maintenance Requests
        $stmtMaint = $this->db->prepare("
            SELECT * FROM `maintenance_requests`
            WHERE `tenant_id` = :tenant_id
              AND `status` NOT IN ('resolved', 'cancelled')
            ORDER BY `created_at` DESC
            LIMIT 5
        ");
        $stmtMaint->execute(['tenant_id' => $tenantId]);
        $activeMaintenance = $stmtMaint->fetchAll(PDO::FETCH_ASSOC);

        return [
            'active_contract' => $activeContract,
            'past_contracts' => $pastContracts,
            'pending_extension' => $pendingExtension,
            'pending_checkout' => $pendingCheckout,
            'total_billed' => (float)($invSummary['total_billed'] ?? 0),
            'total_paid' => $totalPaid,
            'remaining_balance' => (float)($invSummary['remaining_balance'] ?? 0),
            'invoices' => $invoices,
            'payments' => $payments,
            'active_maintenance' => $activeMaintenance,
        ];
    }

    /**
     * Operational statistics for rooms, contracts, and bookings.
     */
    public function getOperationalMetrics(): array
    {
        $rooms = $this->roomRepo->getAll();
        $totalRooms = count($rooms);
        $occupied = 0;
        $vacant = 0;
        $underMaintenance = 0;

        foreach ($rooms as $r) {
            $status = strtolower($r->status ?? '');
            if ($status === 'occupied') {
                $occupied++;
            } elseif ($status === 'maintenance' || $status === 'under_maintenance') {
                $underMaintenance++;
            } else {
                $vacant++;
            }
        }

        $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0.0;

        // Pending Bookings
        $stmtBookings = $this->db->query("SELECT COUNT(id) FROM `bookings` WHERE `status` = 'pending'");
        $pendingBookingsCount = (int)$stmtBookings->fetchColumn();

        // Active Contracts
        $stmtContracts = $this->db->query("SELECT COUNT(id) FROM `rental_contracts` WHERE `status` = 'active'");
        $activeContractsCount = (int)$stmtContracts->fetchColumn();

        // Expiring Contracts within 30 days
        $stmtExpiring = $this->db->query("
            SELECT COUNT(id) FROM `rental_contracts`
            WHERE `status` = 'active'
              AND `end_date` BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ");
        $expiringContractsCount = (int)$stmtExpiring->fetchColumn();

        return [
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupied,
            'vacant_rooms' => $vacant,
            'maintenance_rooms' => $underMaintenance,
            'occupancy_rate' => $occupancyRate,
            'pending_bookings_count' => $pendingBookingsCount,
            'active_contracts_count' => $activeContractsCount,
            'expiring_contracts_count' => $expiringContractsCount,
        ];
    }

    /**
     * Alert badges and action items requiring attention.
     */
    public function getAlertsAndActionItems(): array
    {
        // 1. Upcoming Invoices Due (Next 7 days, unpaid or partially_paid)
        $stmtUpcoming = $this->db->query("
            SELECT COUNT(id) FROM `invoices`
            WHERE `status` IN ('unpaid', 'partially_paid')
              AND `due_date` BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");
        $upcomingDueInvoicesCount = (int)$stmtUpcoming->fetchColumn();

        // 2. Overdue Invoices
        $stmtOverdue = $this->db->query("
            SELECT COUNT(id) FROM `invoices`
            WHERE `status` = 'overdue'
               OR (`status` IN ('unpaid', 'partially_paid') AND `due_date` < CURDATE())
        ");
        $overdueInvoicesCount = (int)$stmtOverdue->fetchColumn();

        // 3. Pending Payment Verifications
        $stmtPendingPay = $this->db->query("
            SELECT COUNT(id) FROM `payments` WHERE `status` = 'pending'
        ");
        $pendingPaymentsCount = (int)$stmtPendingPay->fetchColumn();

        // 4. Urgent / High Priority Maintenance (unresolved)
        $stmtMaint = $this->db->query("
            SELECT COUNT(id) FROM `maintenance_requests`
            WHERE `priority` IN ('urgent', 'high')
              AND `status` NOT IN ('resolved', 'cancelled')
        ");
        $urgentMaintenanceCount = (int)$stmtMaint->fetchColumn();

        // 5. Failed WhatsApp Reminders
        $stmtFailedWa = $this->db->query("
            SELECT COUNT(id) FROM `whatsapp_logs`
            WHERE `status` IN ('failed', 'retrying')
        ");
        $failedWaCount = (int)$stmtFailedWa->fetchColumn();

        return [
            'upcoming_due_invoices_count' => $upcomingDueInvoicesCount,
            'overdue_invoices_count' => $overdueInvoicesCount,
            'pending_payments_count' => $pendingPaymentsCount,
            'urgent_maintenance_count' => $urgentMaintenanceCount,
            'failed_whatsapp_count' => $failedWaCount,
        ];
    }

    /**
     * Revenue per room breakdown for given period.
     */
    public function getRevenuePerRoom(string $dateFrom, string $dateTo): array
    {
        $stmt = $this->db->prepare("
            SELECT r.id AS room_id,
                   r.room_number,
                   r.name AS room_name,
                   COALESCE(SUM(pa.allocated_amount), 0) AS total_revenue
            FROM `rooms` r
            LEFT JOIN `rental_contracts` rc ON rc.room_id = r.id
            LEFT JOIN `invoices` i ON i.contract_id = rc.id
            LEFT JOIN `payment_allocations` pa ON pa.invoice_id = i.id
            LEFT JOIN `payments` p ON p.id = pa.payment_id AND p.status = 'verified' AND p.payment_date BETWEEN :from AND :to
            GROUP BY r.id, r.room_number, r.name
            ORDER BY total_revenue DESC, r.room_number ASC
        ");
        $stmt->execute(['from' => $dateFrom, 'to' => $dateTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Monthly trend of income, expense, and net cash flow for last N months.
     */
    public function getMonthlyTrend(int $months = 6): array
    {
        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $label = date('M Y', strtotime($monthStart));

            $stmtIncome = $this->db->prepare("
                SELECT COALESCE(SUM(amount), 0) FROM `payments`
                WHERE `status` = 'verified' AND `payment_date` BETWEEN :from AND :to
            ");
            $stmtIncome->execute(['from' => $monthStart, 'to' => $monthEnd]);
            $income = (float)$stmtIncome->fetchColumn();

            $stmtExpense = $this->db->prepare("
                SELECT COALESCE(SUM(amount), 0) FROM `expenses`
                WHERE `deleted_at` IS NULL AND `transaction_date` BETWEEN :from AND :to
            ");
            $stmtExpense->execute(['from' => $monthStart, 'to' => $monthEnd]);
            $expense = (float)$stmtExpense->fetchColumn();

            $trend[] = [
                'period' => date('Y-m', strtotime($monthStart)),
                'label' => $label,
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        }

        return $trend;
    }

    /**
     * Recent audit activity logs.
     */
    public function getRecentActivityLogs(int $limit = 10): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.name AS user_name
                FROM `activity_logs` a
                LEFT JOIN `users` u ON u.id = a.user_id
                ORDER BY a.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * List of pending payments needing verification.
     */
    public function getPendingPaymentsList(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name AS tenant_name
            FROM `payments` p
            LEFT JOIN `tenants` t ON t.id = p.tenant_id
            LEFT JOIN `users` u ON u.id = t.user_id
            WHERE p.status = 'pending'
            ORDER BY p.created_at ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * List of urgent/high unresolved maintenance reports.
     */
    public function getUrgentMaintenanceList(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT m.*, r.room_number, u.name AS tenant_name, c.name AS category_name
            FROM `maintenance_requests` m
            LEFT JOIN `rooms` r ON r.id = m.room_id
            LEFT JOIN `tenants` t ON t.id = m.tenant_id
            LEFT JOIN `users` u ON u.id = t.user_id
            LEFT JOIN `maintenance_categories` c ON c.id = m.category_id
            WHERE m.priority IN ('urgent', 'high')
              AND m.status NOT IN ('resolved', 'cancelled')
            ORDER BY FIELD(m.priority, 'urgent', 'high'), m.created_at ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * List of expiring contracts within given days.
     */
    public function getExpiringContractsList(int $days = 30, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, r.room_number, u.name AS tenant_name
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE c.status = 'active'
              AND c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
            ORDER BY c.end_date ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculatePercentageChange(float $previous, float $current): float
    {
        if ($previous == 0.0) {
            return $current > 0.0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / abs($previous)) * 100, 1);
    }
}
