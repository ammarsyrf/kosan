<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ExpenseService;
use Throwable;

class ExpenseController extends Controller
{
    private ExpenseService $expenseService;

    public function __construct(?ExpenseService $expenseService = null)
    {
        $this->expenseService = $expenseService ?? new ExpenseService();
    }

    public function index(Request $request): Response
    {
        $filters = [
            'category_id' => $request->get('category_id'),
            'payment_method' => $request->get('payment_method'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('q'),
        ];

        $page = max(1, (int)$request->get('page', 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $expenses = $this->expenseService->getAllExpenses($filters, $limit, $offset);
        $total = $this->expenseService->countExpenses($filters);
        $totalPages = (int)ceil($total / $limit);

        $categories = $this->expenseService->getAllCategories();

        return $this->render('admin/expenses/index', [
            'title' => 'Pengeluaran Operasional Kos',
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    public function create(): Response
    {
        $categories = $this->expenseService->getAllCategories(true);

        return $this->render('admin/expenses/create', [
            'title' => 'Catat Pengeluaran Baru',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): Response
    {
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        $data = [
            'category_id' => (int)$request->post('category_id'),
            'transaction_date' => (string)$request->post('transaction_date'),
            'amount' => (float)$request->post('amount'),
            'description' => (string)$request->post('description'),
            'vendor' => (string)$request->post('vendor'),
            'payment_method' => (string)$request->post('payment_method', 'cash'),
            'notes' => (string)$request->post('notes'),
        ];

        $file = $_FILES['receipt'] ?? null;

        try {
            $id = $this->expenseService->recordExpense($data, $file, (int)($user['id'] ?? 1), $actor);
            Session::flash('success', 'Pengeluaran operasional berhasil dicatat.');
            return $this->redirect('/admin/expenses/' . $id);
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/admin/expenses/create');
        }
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->param('id');
        $expense = $this->expenseService->getExpenseById($id);

        if (!$expense) {
            Session::flash('error', 'Data pengeluaran tidak ditemukan.');
            return $this->redirect('/admin/expenses');
        }

        return $this->render('admin/expenses/show', [
            'title' => 'Detail Pengeluaran #' . $expense->id,
            'expense' => $expense,
        ]);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->param('id');
        $expense = $this->expenseService->getExpenseById($id);

        if (!$expense) {
            Session::flash('error', 'Data pengeluaran tidak ditemukan.');
            return $this->redirect('/admin/expenses');
        }

        $categories = $this->expenseService->getAllCategories();

        return $this->render('admin/expenses/edit', [
            'title' => 'Edit Pengeluaran #' . $expense->id,
            'expense' => $expense,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        $data = [
            'category_id' => (int)$request->post('category_id'),
            'transaction_date' => (string)$request->post('transaction_date'),
            'amount' => (float)$request->post('amount'),
            'description' => (string)$request->post('description'),
            'vendor' => (string)$request->post('vendor'),
            'payment_method' => (string)$request->post('payment_method', 'cash'),
            'notes' => (string)$request->post('notes'),
        ];

        $file = $_FILES['receipt'] ?? null;

        try {
            $this->expenseService->updateExpense($id, $data, $file, (int)($user['id'] ?? 1), $actor);
            Session::flash('success', 'Data pengeluaran berhasil diperbarui.');
            return $this->redirect('/admin/expenses/' . $id);
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
            return $this->redirect('/admin/expenses/' . $id . '/edit');
        }
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $this->expenseService->deleteExpense($id, $actor);
            Session::flash('success', 'Data pengeluaran berhasil dihapus.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/expenses');
    }

    public function downloadReceipt(Request $request): Response
    {
        $id = (int)$request->param('id');
        $expense = $this->expenseService->getExpenseById($id);

        if (!$expense || empty($expense->receipt_path)) {
            return new Response('File bukti pengeluaran tidak ditemukan.', 404);
        }

        $fullPath = dirname(__DIR__, 2) . '/storage/uploads/' . $expense->receipt_path;
        if (!file_exists($fullPath)) {
            return new Response('File fisik tidak ditemukan di server storage.', 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fullPath);
        if (PHP_VERSION_ID < 80500) {
            finfo_close($finfo);
        }

        $content = file_get_contents($fullPath);
        $filename = basename($fullPath);

        return new Response($content, 200, [
            'Content-Type' => $mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length' => (string)filesize($fullPath),
        ]);
    }

    // --- Category Management ---
    public function categories(): Response
    {
        $categories = $this->expenseService->getAllCategories();

        return $this->render('admin/expenses/categories', [
            'title' => 'Kategori Pengeluaran',
            'categories' => $categories,
        ]);
    }

    public function storeCategory(Request $request): Response
    {
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        $data = [
            'name' => (string)$request->post('name'),
            'description' => (string)$request->post('description'),
            'is_active' => $request->post('is_active') ? 1 : 0,
        ];

        try {
            $this->expenseService->createCategory($data, $actor);
            Session::flash('success', 'Kategori pengeluaran baru berhasil ditambahkan.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/expenses/categories');
    }

    public function updateCategory(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        $data = [
            'name' => (string)$request->post('name'),
            'description' => (string)$request->post('description'),
            'is_active' => $request->post('is_active') ? 1 : 0,
        ];

        try {
            $this->expenseService->updateCategory($id, $data, $actor);
            Session::flash('success', 'Kategori pengeluaran berhasil diperbarui.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/expenses/categories');
    }

    public function deleteCategory(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $this->expenseService->deleteCategory($id, $actor);
            Session::flash('success', 'Kategori pengeluaran berhasil dihapus.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/expenses/categories');
    }
}
