<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Repositories\ExpenseRepository;
use InvalidArgumentException;
use RuntimeException;

class ExpenseService
{
    private ExpenseRepository $expenseRepo;

    public function __construct(?ExpenseRepository $expenseRepo = null)
    {
        $this->expenseRepo = $expenseRepo ?? new ExpenseRepository();
    }

    // --- Categories ---
    public function getAllCategories(bool $activeOnly = false): array
    {
        return $this->expenseRepo->getAllCategories($activeOnly);
    }

    public function getCategoryById(int $id): ?ExpenseCategory
    {
        return $this->expenseRepo->findCategoryById($id);
    }

    public function createCategory(array $data, ?string $actor = 'Admin'): int
    {
        $name = trim((string)($data['name'] ?? ''));
        if (empty($name)) {
            throw new InvalidArgumentException("Nama kategori pengeluaran wajib diisi.");
        }

        $id = $this->expenseRepo->createCategory([
            'name' => $name,
            'description' => !empty($data['description']) ? trim((string)$data['description']) : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);

        Logger::audit((string)$actor, 'CREATE_EXPENSE_CATEGORY', 'expense_categories', $id, ['name' => $name]);

        return $id;
    }

    public function updateCategory(int $id, array $data, ?string $actor = 'Admin'): bool
    {
        $category = $this->expenseRepo->findCategoryById($id);
        if (!$category) {
            throw new RuntimeException("Kategori pengeluaran tidak ditemukan.");
        }

        $name = trim((string)($data['name'] ?? ''));
        if (empty($name)) {
            throw new InvalidArgumentException("Nama kategori pengeluaran tidak boleh kosong.");
        }

        $res = $this->expenseRepo->updateCategory($id, [
            'name' => $name,
            'description' => !empty($data['description']) ? trim((string)$data['description']) : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);

        Logger::audit((string)$actor, 'UPDATE_EXPENSE_CATEGORY', 'expense_categories', $id, ['name' => $name]);

        return $res;
    }

    public function deleteCategory(int $id, ?string $actor = 'Admin'): bool
    {
        $category = $this->expenseRepo->findCategoryById($id);
        if (!$category) {
            throw new RuntimeException("Kategori pengeluaran tidak ditemukan.");
        }

        $count = $this->expenseRepo->countExpensesByCategoryId($id);
        if ($count > 0) {
            throw new RuntimeException("Kategori '{$category->name}' tidak dapat dihapus karena telah digunakan pada {$count} transaksi pengeluaran. Nonaktifkan kategori sebagai alternatif.");
        }

        $res = $this->expenseRepo->deleteCategory($id);
        Logger::audit((string)$actor, 'DELETE_EXPENSE_CATEGORY', 'expense_categories', $id, ['name' => $category->name]);

        return $res;
    }

    // --- Expenses ---
    public function getAllExpenses(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        return $this->expenseRepo->getAll($filters, $limit, $offset);
    }

    public function countExpenses(array $filters = []): int
    {
        return $this->expenseRepo->count($filters);
    }

    public function getExpenseById(int $id): ?Expense
    {
        return $this->expenseRepo->findById($id);
    }

    public function recordExpense(array $data, ?array $file = null, int $userId = 1, ?string $actor = 'Admin'): int
    {
        $this->validateExpenseData($data);

        $receiptPath = $this->handleReceiptUpload($file);

        $expenseId = $this->expenseRepo->create([
            'category_id' => (int)$data['category_id'],
            'transaction_date' => (string)$data['transaction_date'],
            'amount' => (float)$data['amount'],
            'description' => trim((string)$data['description']),
            'vendor' => !empty($data['vendor']) ? trim((string)$data['vendor']) : null,
            'payment_method' => (string)($data['payment_method'] ?? 'cash'),
            'receipt_path' => $receiptPath,
            'created_by' => $userId,
            'notes' => !empty($data['notes']) ? trim((string)$data['notes']) : null,
        ]);

        Logger::audit((string)$actor, 'CREATE_EXPENSE', 'expenses', $expenseId, [
            'amount' => (float)$data['amount'],
            'category_id' => (int)$data['category_id'],
            'transaction_date' => (string)$data['transaction_date'],
        ]);

        return $expenseId;
    }

    public function updateExpense(int $id, array $data, ?array $file = null, int $userId = 1, ?string $actor = 'Admin'): bool
    {
        $expense = $this->expenseRepo->findById($id);
        if (!$expense) {
            throw new RuntimeException("Data pengeluaran tidak ditemukan.");
        }

        $this->validateExpenseData($data);

        $receiptPath = $this->handleReceiptUpload($file);

        $res = $this->expenseRepo->update($id, [
            'category_id' => (int)$data['category_id'],
            'transaction_date' => (string)$data['transaction_date'],
            'amount' => (float)$data['amount'],
            'description' => trim((string)$data['description']),
            'vendor' => !empty($data['vendor']) ? trim((string)$data['vendor']) : null,
            'payment_method' => (string)($data['payment_method'] ?? 'cash'),
            'receipt_path' => $receiptPath,
            'notes' => !empty($data['notes']) ? trim((string)$data['notes']) : null,
        ]);

        Logger::audit((string)$actor, 'UPDATE_EXPENSE', 'expenses', $id, [
            'old_amount' => $expense->amount,
            'new_amount' => (float)$data['amount'],
            'category_id' => (int)$data['category_id'],
        ]);

        return $res;
    }

    public function deleteExpense(int $id, ?string $actor = 'Admin'): bool
    {
        $expense = $this->expenseRepo->findById($id);
        if (!$expense) {
            throw new RuntimeException("Data pengeluaran tidak ditemukan.");
        }

        $res = $this->expenseRepo->delete($id);

        Logger::audit((string)$actor, 'DELETE_EXPENSE', 'expenses', $id, [
            'amount' => $expense->amount,
            'description' => $expense->description,
        ]);

        return $res;
    }

    private function validateExpenseData(array $data): void
    {
        if (empty($data['category_id'])) {
            throw new InvalidArgumentException("Kategori pengeluaran wajib dipilih.");
        }

        $amount = (float)($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw new InvalidArgumentException("Nominal pengeluaran harus lebih besar dari 0.");
        }

        if (empty($data['transaction_date'])) {
            throw new InvalidArgumentException("Tanggal transaksi wajib diisi.");
        }

        if (empty($data['description'])) {
            throw new InvalidArgumentException("Deskripsi pengeluaran wajib diisi.");
        }

        // Verify category exists and is active (unless updating old transaction with same category)
        $cat = $this->expenseRepo->findCategoryById((int)$data['category_id']);
        if (!$cat) {
            throw new RuntimeException("Kategori pengeluaran tidak valid.");
        }
    }

    public function handleReceiptUpload(?array $file): ?string
    {
        if (!$file || empty($file['tmp_name']) || empty($file['name'])) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Gagal mengunggah file bukti transaksi (kode: {$file['error']}).");
        }

        $origName = basename((string)$file['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
        if (!in_array($ext, $allowedExts, true)) {
            throw new RuntimeException("Ekstensi file bukti tidak diizinkan. Gunakan JPG, PNG, WEBP, atau PDF.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        if (PHP_VERSION_ID < 80500) {
            finfo_close($finfo);
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($mime, $allowedMimes, true)) {
            throw new RuntimeException("Tipe konten file bukti tidak valid ({$mime}).");
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxSize) {
            throw new RuntimeException("Ukuran file bukti pengeluaran maksimal 5 MB.");
        }

        $uploadDir = dirname(__DIR__, 2) . '/storage/uploads/expenses';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $randomName = 'receipt_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetPath = $uploadDir . '/' . $randomName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            if (!copy($file['tmp_name'], $targetPath)) {
                throw new RuntimeException("Gagal menyimpan file bukti pengeluaran ke storage.");
            }
        }

        return 'expenses/' . $randomName;
    }
}
