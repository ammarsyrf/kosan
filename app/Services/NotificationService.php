<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Invoice;
use App\Models\NotificationTemplate;
use App\Models\WhatsAppLog;
use App\Repositories\InvoiceRepository;
use App\Repositories\NotificationRepository;
use PDO;
use RuntimeException;

class NotificationService
{
    public const ALLOWED_PLACEHOLDERS = [
        '{{tenant_name}}',
        '{{room_name}}',
        '{{invoice_number}}',
        '{{period_start}}',
        '{{period_end}}',
        '{{due_date}}',
        '{{total_amount}}',
        '{{paid_amount}}',
        '{{remaining_balance}}',
        '{{business_name}}',
    ];

    private NotificationRepository $notifRepo;
    private InvoiceRepository $invoiceRepo;
    private FonnteService $fonnteService;

    public function __construct(
        ?NotificationRepository $notifRepo = null,
        ?InvoiceRepository $invoiceRepo = null,
        ?FonnteService $fonnteService = null
    ) {
        $this->notifRepo = $notifRepo ?? new NotificationRepository();
        $this->invoiceRepo = $invoiceRepo ?? new InvoiceRepository();
        $this->fonnteService = $fonnteService ?? new FonnteService();
    }

    public function getAllTemplates(): array
    {
        return $this->notifRepo->getAllTemplates();
    }

    public function getTemplateByCode(string $code): ?NotificationTemplate
    {
        return $this->notifRepo->findTemplateByCode($code);
    }

    public function updateTemplate(int $id, string $content, int $isActive): bool
    {
        return $this->notifRepo->updateTemplate($id, $content, $isActive);
    }

    public function validateTemplateContent(string $content): void
    {
        if (preg_match_all('/\{\{?([a-zA-Z0-9_]+)\}?\}/', $content, $matches)) {
            $allowedClean = array_map(fn($p) => trim($p, '{}'), self::ALLOWED_PLACEHOLDERS);
            $allowedClean[] = 'room_number';
            $allowedClean[] = 'balance';

            foreach ($matches[1] as $placeholder) {
                if (!in_array($placeholder, $allowedClean, true)) {
                    throw new RuntimeException("Placeholder '{{{$placeholder}}}' tidak dikenal dalam sistem.");
                }
            }
        }
    }

    public function getAllLogs(array $filters = []): array
    {
        return $this->notifRepo->getAllLogs($filters);
    }

    public function renderTemplate(string $rawContent, array $variables): string
    {
        $rendered = $rawContent;

        // Support both {{var}} and {var}
        foreach ($variables as $key => $value) {
            $valStr = (string)$value;
            $rendered = str_replace([
                "{{" . $key . "}}",
                "{" . $key . "}",
            ], $valStr, $rendered);
        }

        // Validate if any unreplaced placeholder remains e.g. {{something}} or {something}
        if (preg_match('/\{\{([a-zA-Z0-9_]+)\}\}/', $rendered, $m) || preg_match('/\{([a-zA-Z0-9_]+)\}/', $rendered, $m)) {
            throw new RuntimeException("Terdapat placeholder yang belum terisi: " . $m[0]);
        }

        return $rendered;
    }

    public function buildInvoiceVariables(Invoice $invoice): array
    {
        $pdo = Database::connect();
        $bizName = (string)$pdo->query("SELECT `value` FROM `settings` WHERE `key` = 'business_name'")->fetchColumn() ?: 'Kost Management';

        return [
            'tenant_name' => $invoice->tenant_name,
            'room_name' => $invoice->room_number,
            'room_number' => $invoice->room_number,
            'invoice_number' => $invoice->invoice_number,
            'period_start' => $invoice->period_start,
            'period_end' => $invoice->period_end,
            'due_date' => $invoice->due_date,
            'total_amount' => $invoice->formattedTotal(),
            'paid_amount' => $invoice->formattedPaid(),
            'remaining_balance' => $invoice->formattedBalance(),
            'balance' => $invoice->formattedBalance(),
            'business_name' => $bizName,
        ];
    }

    public function sendInvoiceReminder(Invoice $invoice, string $templateCode, string $scheduledDate): array
    {
        // Business Rules Checks:
        // 1. Invoice must NOT be paid or cancelled/void
        if ($invoice->isPaid() || in_array($invoice->status, ['cancelled', 'void'], true)) {
            return [
                'skipped' => true,
                'reason' => "Invoice #{$invoice->invoice_number} sudah lunas atau dibatalkan.",
            ];
        }

        // 2. Tenant phone must exist
        if (empty($invoice->tenant_phone)) {
            return [
                'skipped' => true,
                'reason' => "Penyewa {$invoice->tenant_name} tidak memiliki nomor telepon terdaftar.",
            ];
        }

        // 3. Get notification template
        $template = $this->getTemplateByCode($templateCode);
        if (!$template || !$template->isActive()) {
            return [
                'skipped' => true,
                'reason' => "Template notifikasi [{$templateCode}] tidak aktif atau tidak ditemukan.",
            ];
        }

        // 4. Generate Idempotency Key
        $idempotencyKey = sprintf("inv_%d_%s_%s", $invoice->id, $templateCode, $scheduledDate);

        // 5. Check if already recorded/sent
        $existingLog = $this->notifRepo->findLogByIdempotencyKey($idempotencyKey);
        if ($existingLog && $existingLog->isSent()) {
            return [
                'skipped' => true,
                'reason' => "Pesan pengingat untuk invoice #{$invoice->invoice_number} ({$templateCode}) pada tanggal {$scheduledDate} sudah pernah terkirim.",
                'log_id' => $existingLog->id,
            ];
        }

        // 6. Render template with variables
        $variables = $this->buildInvoiceVariables($invoice);
        $message = $this->renderTemplate($template->content, $variables);

        // 7. Atomic Log Creation / Acquisition
        $logId = 0;
        if (!$existingLog) {
            $logId = $this->notifRepo->createLog([
                'idempotency_key' => $idempotencyKey,
                'recipient_phone' => $invoice->tenant_phone,
                'message' => $message,
                'status' => 'pending',
            ]);
        } else {
            $logId = $existingLog->id;
        }

        // 8. Dispatch via Fonnte Service
        $sendResult = $this->fonnteService->sendWhatsAppMessage($invoice->tenant_phone, $message);

        // 9. Update Log Status
        $finalStatus = $sendResult['success'] ? 'sent' : ($sendResult['status'] === 'disabled' ? 'pending' : 'failed');
        $this->notifRepo->updateLogStatus($logId, $finalStatus, $sendResult['raw']);

        return [
            'skipped' => false,
            'success' => $sendResult['success'],
            'status' => $finalStatus,
            'log_id' => $logId,
            'message' => $sendResult['message'],
        ];
    }

    public function retryLog(int $logId, ?string $actor = 'Admin'): array
    {
        $log = $this->notifRepo->findLogById($logId);
        if (!$log) {
            throw new RuntimeException("Log pengiriman pesan tidak ditemukan.");
        }

        if ($log->isSent()) {
            throw new RuntimeException("Pesan ini sudah berstatus terkirim (sent) dan tidak perlu di-retry.");
        }

        if ($log->retry_count >= 3 && $actor !== 'Admin') {
            throw new RuntimeException("Batas maksimal retry otomatis (3x) telah tercapai.");
        }

        // Mark as retrying
        $this->notifRepo->updateLogStatus($logId, 'retrying', $log->response_payload);

        // Dispatch
        $sendResult = $this->fonnteService->sendWhatsAppMessage($log->recipient_phone, $log->message);
        $finalStatus = $sendResult['success'] ? 'sent' : 'failed';

        $this->notifRepo->incrementRetryCount($logId, $finalStatus, $sendResult['raw']);

        Logger::audit((string)$actor, 'RETRY_WHATSAPP_MESSAGE', 'whatsapp_logs', $logId, [
            'status' => $finalStatus,
            'retry_count' => $log->retry_count + 1,
        ]);

        return [
            'success' => $sendResult['success'],
            'status' => $finalStatus,
            'message' => $sendResult['message'],
        ];
    }

    public function processAutomaticRetries(int $maxRetries = 3): int
    {
        $pendingLogs = $this->notifRepo->getPendingRetries($maxRetries);
        $successCount = 0;

        foreach ($pendingLogs as $log) {
            try {
                $res = $this->retryLog($log->id, 'SystemRetryCron');
                if ($res['success']) {
                    $successCount++;
                }
            } catch (\Throwable $e) {
                Logger::error("Auto retry failed for Log #{$log->id}: " . $e->getMessage());
            }
        }

        return $successCount;
    }
}
