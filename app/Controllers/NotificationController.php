<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use Throwable;

class NotificationController extends Controller
{
    private NotificationRepository $notifRepo;
    private NotificationService $notifService;

    public function __construct(
        ?NotificationRepository $notifRepo = null,
        ?NotificationService $notifService = null
    ) {
        $this->notifRepo = $notifRepo ?? new NotificationRepository();
        $this->notifService = $notifService ?? new NotificationService();
    }

    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $phone = $request->get('phone');

        $filters = [];
        if ($status !== null && $status !== '') {
            $filters['status'] = (string)$status;
        }
        if ($phone !== null && $phone !== '') {
            $filters['phone'] = (string)$phone;
        }

        $logs = $this->notifRepo->getAllLogs($filters);

        return $this->render('admin/notifications/index', [
            'title' => 'Log Notifikasi WhatsApp',
            'logs' => $logs,
            'currentStatus' => $status,
            'currentPhone' => $phone,
        ]);
    }

    public function templates(Request $request): Response
    {
        $templates = $this->notifRepo->getAllTemplates();

        return $this->render('admin/notifications/templates', [
            'title' => 'Template Pesan WhatsApp',
            'templates' => $templates,
            'placeholders' => NotificationService::ALLOWED_PLACEHOLDERS,
        ]);
    }

    public function updateTemplate(Request $request): Response
    {
        $id = (int)$request->post('id');
        $content = trim((string)$request->post('content'));
        $isActive = $request->post('is_active') ? 1 : 0;

        if ($id <= 0 || empty($content)) {
            Session::flash('error', 'Konten template tidak boleh kosong.');
            return $this->redirect('/admin/notifications/templates');
        }

        try {
            $this->notifService->validateTemplateContent($content);
            $this->notifRepo->updateTemplate($id, $content, $isActive);

            Session::flash('success', 'Template pesan WhatsApp berhasil diperbarui.');
        } catch (Throwable $e) {
            Session::flash('error', 'Gagal memperbarui template: ' . $e->getMessage());
        }

        return $this->redirect('/admin/notifications/templates');
    }

    public function settings(Request $request): Response
    {
        $settings = $this->notifRepo->getNotificationSettings();
        $fonnteEnabled = strtolower((string)getenv('FONNTE_ENABLED')) === 'true';

        return $this->render('admin/notifications/settings', [
            'title' => 'Pengaturan Notifikasi WhatsApp',
            'settings' => $settings,
            'fonnteEnabled' => $fonnteEnabled,
        ]);
    }

    public function updateSettings(Request $request): Response
    {
        $user = Session::get('user');
        if (($user['role'] ?? '') !== 'owner') {
            Session::flash('error', 'Hanya Owner yang dapat mengubah pengaturan notifikasi.');
            return $this->redirect('/admin/notifications/settings');
        }

        $schedules = [
            'reminder_h7_active',
            'reminder_h3_active',
            'reminder_h1_active',
            'reminder_due_active',
            'reminder_overdue_active',
        ];

        foreach ($schedules as $key) {
            $val = $request->post($key) ? '1' : '0';
            $this->notifRepo->updateNotificationSetting($key, $val);
        }

        Session::flash('success', 'Pengaturan jadwal notifikasi pengingat berhasil disimpan.');
        return $this->redirect('/admin/notifications/settings');
    }

    public function retry(Request $request): Response
    {
        $id = (int)$request->param('id');
        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $result = $this->notifService->retryLog($id, $actor);
            if ($result['success']) {
                Session::flash('success', 'Pesan WhatsApp berhasil dikirim ulang.');
            } else {
                Session::flash('warning', 'Pesan telah di-retry namun gagal dikirim: ' . $result['message']);
            }
        } catch (Throwable $e) {
            Session::flash('error', 'Gagal mengirim ulang pesan: ' . $e->getMessage());
        }

        return $this->redirect('/admin/notifications');
    }
}
