<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;
use App\Models\MaintenanceAttachment;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceRequest;
use App\Repositories\MaintenanceRepository;
use InvalidArgumentException;
use RuntimeException;

class MaintenanceService
{
    private MaintenanceRepository $maintRepo;

    public const ALLOWED_STATUSES = ['submitted', 'reviewed', 'in_progress', 'waiting', 'resolved', 'rejected'];
    public const ALLOWED_PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    public function __construct(?MaintenanceRepository $maintRepo = null)
    {
        $this->maintRepo = $maintRepo ?? new MaintenanceRepository();
    }

    // --- Categories ---
    public function getAllCategories(): array
    {
        return $this->maintRepo->getAllCategories();
    }

    public function getCategoryById(int $id): ?MaintenanceCategory
    {
        return $this->maintRepo->findCategoryById($id);
    }

    public function createCategory(array $data, ?string $actor = 'Owner'): int
    {
        $name = trim((string)($data['name'] ?? ''));
        if (empty($name)) {
            throw new InvalidArgumentException("Nama kategori maintenance wajib diisi.");
        }

        $id = $this->maintRepo->createCategory([
            'name' => $name,
            'description' => !empty($data['description']) ? trim((string)$data['description']) : null,
        ]);

        Logger::audit((string)$actor, 'CREATE_MAINTENANCE_CATEGORY', 'maintenance_categories', $id, ['name' => $name]);

        return $id;
    }

    // --- Requests ---
    public function getAllRequests(array $filters = []): array
    {
        return $this->maintRepo->getAll($filters);
    }

    public function getTenantRequests(int $tenantId): array
    {
        return $this->maintRepo->getByTenantId($tenantId);
    }

    public function getRequestById(int $id, bool $includeInternal = true): ?MaintenanceRequest
    {
        $request = $this->maintRepo->findById($id);
        if (!$request) {
            return null;
        }

        $request->updates = $this->maintRepo->getUpdatesByRequestId($id, $includeInternal);

        return $request;
    }

    public function createRequest(
        int $tenantId,
        int $roomId,
        int $categoryId,
        string $title,
        string $description,
        string $priority = 'medium',
        array $files = [],
        ?string $actor = 'Tenant'
    ): int {
        $title = trim($title);
        $description = trim($description);

        if (empty($title)) {
            throw new InvalidArgumentException("Judul laporan kerusakan wajib diisi.");
        }

        if (empty($description)) {
            throw new InvalidArgumentException("Deskripsi kerusakan wajib diisi.");
        }

        if (!in_array($priority, self::ALLOWED_PRIORITIES, true)) {
            $priority = 'medium';
        }

        $cat = $this->maintRepo->findCategoryById($categoryId);
        if (!$cat) {
            throw new RuntimeException("Kategori perbaikan tidak valid.");
        }

        $requestId = $this->maintRepo->create([
            'tenant_id' => $tenantId,
            'room_id' => $roomId,
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => 'submitted',
        ]);

        // Initial system update entry
        $this->maintRepo->createUpdate([
            'request_id' => $requestId,
            'user_id' => 1, // System / Admin
            'status_changed_to' => 'submitted',
            'notes' => 'Laporan perbaikan dibuat oleh penyewa.',
            'is_internal' => 0,
        ]);

        // Process attachments if any
        if (!empty($files)) {
            $this->processAttachments($requestId, $files);
        }

        Logger::audit((string)$actor, 'CREATE_MAINTENANCE_REQUEST', 'maintenance_requests', $requestId, [
            'tenant_id' => $tenantId,
            'room_id' => $roomId,
            'title' => $title,
            'priority' => $priority,
        ]);

        return $requestId;
    }

    public function updateStatus(
        int $requestId,
        string $newStatus,
        int $actorUserId,
        string $notes = '',
        ?string $actorName = 'Admin'
    ): bool {
        $request = $this->maintRepo->findById($requestId);
        if (!$request) {
            throw new RuntimeException("Laporan perbaikan tidak ditemukan.");
        }

        if (!in_array($newStatus, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException("Status perbaikan tidak valid ({$newStatus}).");
        }

        $oldStatus = $request->status;
        $resolvedAt = ($newStatus === 'resolved') ? date('Y-m-d H:i:s') : null;

        $this->maintRepo->updateStatus($requestId, $newStatus, $resolvedAt);

        // Record history update
        $updateNote = !empty($notes) ? $notes : "Status diperbarui dari [{$oldStatus}] menjadi [{$newStatus}].";
        $this->maintRepo->createUpdate([
            'request_id' => $requestId,
            'user_id' => $actorUserId,
            'status_changed_to' => $newStatus,
            'notes' => $updateNote,
            'is_internal' => 0,
        ]);

        Logger::audit((string)$actorName, 'UPDATE_MAINTENANCE_STATUS', 'maintenance_requests', $requestId, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $updateNote,
        ]);

        return true;
    }

    public function updatePriority(
        int $requestId,
        string $newPriority,
        int $actorUserId,
        string $notes = '',
        ?string $actorName = 'Admin'
    ): bool {
        $request = $this->maintRepo->findById($requestId);
        if (!$request) {
            throw new RuntimeException("Laporan perbaikan tidak ditemukan.");
        }

        if (!in_array($newPriority, self::ALLOWED_PRIORITIES, true)) {
            throw new InvalidArgumentException("Prioritas perbaikan tidak valid ({$newPriority}).");
        }

        $oldPriority = $request->priority;
        $this->maintRepo->updatePriority($requestId, $newPriority);

        $updateNote = !empty($notes) ? $notes : "Prioritas diubah dari [{$oldPriority}] menjadi [{$newPriority}].";
        $this->maintRepo->createUpdate([
            'request_id' => $requestId,
            'user_id' => $actorUserId,
            'status_changed_to' => null,
            'notes' => $updateNote,
            'is_internal' => 0,
        ]);

        Logger::audit((string)$actorName, 'UPDATE_MAINTENANCE_PRIORITY', 'maintenance_requests', $requestId, [
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority,
        ]);

        return true;
    }

    public function addUpdate(
        int $requestId,
        int $userId,
        string $notes,
        bool $isInternal = false,
        ?string $actorName = 'User'
    ): int {
        $request = $this->maintRepo->findById($requestId);
        if (!$request) {
            throw new RuntimeException("Laporan perbaikan tidak ditemukan.");
        }

        $notes = trim($notes);
        if (empty($notes)) {
            throw new InvalidArgumentException("Catatan update tidak boleh kosong.");
        }

        $updateId = $this->maintRepo->createUpdate([
            'request_id' => $requestId,
            'user_id' => $userId,
            'status_changed_to' => null,
            'notes' => $notes,
            'is_internal' => $isInternal ? 1 : 0,
        ]);

        Logger::audit((string)$actorName, 'ADD_MAINTENANCE_UPDATE', 'maintenance_updates', $updateId, [
            'request_id' => $requestId,
            'is_internal' => $isInternal ? 1 : 0,
        ]);

        return $updateId;
    }

    public function processAttachments(int $requestId, array $files): array
    {
        $attachmentIds = [];
        $uploadDir = dirname(__DIR__, 2) . '/storage/uploads/maintenance';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5 MB

        // Normalize single or multiple file array
        $normalizedFiles = [];
        if (isset($files[0]['tmp_name'])) {
            $normalizedFiles = $files;
        } elseif (isset($files['name']) && is_array($files['name'])) {
            foreach ($files['name'] as $idx => $name) {
                if (!empty($name) && ($files['error'][$idx] ?? 1) === UPLOAD_ERR_OK) {
                    $normalizedFiles[] = [
                        'name' => $name,
                        'type' => $files['type'][$idx] ?? '',
                        'tmp_name' => $files['tmp_name'][$idx] ?? '',
                        'error' => $files['error'][$idx] ?? 0,
                        'size' => $files['size'][$idx] ?? 0,
                    ];
                }
            }
        } elseif (isset($files['tmp_name']) && !empty($files['tmp_name'])) {
            $normalizedFiles[] = $files;
        }

        foreach ($normalizedFiles as $file) {
            if (empty($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                continue;
            }

            if ($file['size'] > $maxSize) {
                throw new RuntimeException("Ukuran file lampiran maksimal 5 MB.");
            }

            $origName = basename((string)$file['name']);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts, true)) {
                throw new RuntimeException("Format file tidak diizinkan ({$ext}). Gunakan JPG, PNG, WEBP, atau PDF.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            if (PHP_VERSION_ID < 80500) {
                finfo_close($finfo);
            }

            if (!in_array($mime, $allowedMimes, true)) {
                throw new RuntimeException("Tipe konten file tidak valid ({$mime}).");
            }

            $randomName = 'maint_' . $requestId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $targetPath = $uploadDir . '/' . $randomName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                if (!copy($file['tmp_name'], $targetPath)) {
                    throw new RuntimeException("Gagal menyimpan lampiran perbaikan.");
                }
            }

            $attId = $this->maintRepo->createAttachment([
                'request_id' => $requestId,
                'file_path' => 'maintenance/' . $randomName,
                'file_name' => $origName,
                'mime_type' => $mime,
            ]);

            $attachmentIds[] = $attId;
        }

        return $attachmentIds;
    }

    public function getAttachmentById(int $id): ?MaintenanceAttachment
    {
        return $this->maintRepo->findAttachmentById($id);
    }
}
