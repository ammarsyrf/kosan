<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class FinancialReportService
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getSummary(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $dateFrom = $dateFrom ?? date('Y-m-01'); // default: start of current month
        $dateTo = $dateTo ?? date('Y-m-t'); // default: end of current month

        // 1. Total Invoices Issued (Billed)
        $stmtInv = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) AS total_billed,
                   COUNT(id) AS total_count
            FROM `invoices`
            WHERE `issue_date` BETWEEN :from AND :to
              AND `status` NOT IN ('cancelled', 'void')
        ");
        $stmtInv->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $invData = $stmtInv->fetch(PDO::FETCH_ASSOC);
        $totalBilled = (float)($invData['total_billed'] ?? 0);
        $invoicesCount = (int)($invData['total_count'] ?? 0);

        // 2. Total Verified Revenue (Income) — ONLY status = 'verified'
        $stmtPay = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) AS total_income,
                   COUNT(id) AS payment_count
            FROM `payments`
            WHERE `status` = 'verified'
              AND `payment_date` BETWEEN :from AND :to
        ");
        $stmtPay->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $payData = $stmtPay->fetch(PDO::FETCH_ASSOC);
        $totalIncome = (float)($payData['total_income'] ?? 0);
        $verifiedPaymentsCount = (int)($payData['payment_count'] ?? 0);

        // 3. Total Outstanding Receivables (Piutang Berjalan)
        $stmtRec = $this->db->prepare("
            SELECT COALESCE(SUM(balance), 0) AS total_receivables
            FROM `invoices`
            WHERE `status` IN ('unpaid', 'partially_paid', 'overdue')
              AND `issue_date` <= :to
        ");
        $stmtRec->execute(['to' => $dateTo]);
        $totalReceivables = (float)$stmtRec->fetchColumn();

        // 4. Total Operational Expenses
        $stmtExp = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) AS total_expense,
                   COUNT(id) AS expense_count
            FROM `expenses`
            WHERE `deleted_at` IS NULL
              AND `transaction_date` BETWEEN :from AND :to
        ");
        $stmtExp->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $expData = $stmtExp->fetch(PDO::FETCH_ASSOC);
        $totalExpense = (float)($expData['total_expense'] ?? 0);
        $expensesCount = (int)($expData['expense_count'] ?? 0);

        // 5. Net Cash Flow
        $netCashFlow = $totalIncome - $totalExpense;

        // 6. Expenses by Category Breakdown
        $stmtCatExp = $this->db->prepare("
            SELECT c.id, c.name, COALESCE(SUM(e.amount), 0) AS total, COUNT(e.id) AS count
            FROM `expense_categories` c
            JOIN `expenses` e ON e.category_id = c.id AND e.deleted_at IS NULL
            WHERE e.transaction_date BETWEEN :from AND :to
            GROUP BY c.id, c.name
            ORDER BY total DESC
        ");
        $stmtCatExp->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $expensesByCategory = $stmtCatExp->fetchAll(PDO::FETCH_ASSOC);

        // 7. Revenue by Period (Monthly breakdown)
        $stmtRevPeriod = $this->db->prepare("
            SELECT DATE_FORMAT(payment_date, '%Y-%m') AS period,
                   COALESCE(SUM(amount), 0) AS total,
                   COUNT(id) AS count
            FROM `payments`
            WHERE `status` = 'verified'
              AND `payment_date` BETWEEN :from AND :to
            GROUP BY period
            ORDER BY period ASC
        ");
        $stmtRevPeriod->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $revenueByPeriod = $stmtRevPeriod->fetchAll(PDO::FETCH_ASSOC);

        // 8. Payment Breakdown by Method
        $stmtPayMethod = $this->db->prepare("
            SELECT payment_method, COUNT(id) AS count, COALESCE(SUM(amount), 0) AS total
            FROM `payments`
            WHERE `status` = 'verified'
              AND `payment_date` BETWEEN :from AND :to
            GROUP BY payment_method
        ");
        $stmtPayMethod->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $paymentsByMethod = $stmtPayMethod->fetchAll(PDO::FETCH_ASSOC);

        // 9. Expense Breakdown by Payment Method
        $stmtExpMethod = $this->db->prepare("
            SELECT payment_method, COUNT(id) AS count, COALESCE(SUM(amount), 0) AS total
            FROM `expenses`
            WHERE `deleted_at` IS NULL
              AND `transaction_date` BETWEEN :from AND :to
            GROUP BY payment_method
        ");
        $stmtExpMethod->execute(['from' => $dateFrom, 'to' => $dateTo]);
        $expensesByMethod = $stmtExpMethod->fetchAll(PDO::FETCH_ASSOC);

        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'total_billed' => $totalBilled,
            'invoices_count' => $invoicesCount,
            'total_income' => $totalIncome,
            'verified_payments_count' => $verifiedPaymentsCount,
            'total_receivables' => $totalReceivables,
            'total_expense' => $totalExpense,
            'expenses_count' => $expensesCount,
            'net_cash_flow' => $netCashFlow,
            'expenses_by_category' => $expensesByCategory,
            'revenue_by_period' => $revenueByPeriod,
            'payments_by_method' => $paymentsByMethod,
            'expenses_by_method' => $expensesByMethod,
        ];
    }
}
