<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\RoomRepository;
use App\Services\MaintenanceService;
use Throwable;

class MaintenanceController extends Controller
{
    private MaintenanceService $maintService;
    private RoomRepository $roomRepo;

    public function __construct(
        ?MaintenanceService $maintService = null,
        ?RoomRepository $roomRepo = null
    ) {
        $this->maintService = $maintService ?? new MaintenanceService();
        $this->roomRepo = $roomRepo ?? new RoomRepository();
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
            'category_id' => $request->get('category_id'),
            'room_id' => $request->get('room_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $requests = $this->maintService->getAllRequests($filters);
        $categories = $this->maintService->getAllCategories();
        $rooms = $this->roomRepo->getAll();

        return $this->render('admin/maintenance/index', [
            'title' => 'Laporan Perbaikan & Kerusakan (Maintenance)',
            'requests' => $requests,
            'categories' => $categories,
            'rooms' => $rooms,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->param('id');
        $maintRequest = $this->maintService->getRequestById($id, true);

        if (!$maintRequest) {
            Session::flash('error', 'Laporan perbaikan tidak ditemukan.');
            return $this->redirect('/admin/maintenance');
        }

        return $this->render('admin/maintenance/show', [
            'title' => 'Laporan #' . $maintRequest->id . ' — ' . $maintRequest->title,
            'req' => $maintRequest,
            'allowedStatuses' => MaintenanceService::ALLOWED_STATUSES,
            'allowedPriorities' => MaintenanceService::ALLOWED_PRIORITIES,
        ]);
    }

    public function updateStatus(Request $request): Response
    {
        $id = (int)$request->param('id');
        $status = (string)$request->post('status');
        $notes = (string)$request->post('notes', '');

        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $this->maintService->updateStatus($id, $status, (int)($user['id'] ?? 1), $notes, $actor);
            Session::flash('success', 'Status laporan perbaikan berhasil diperbarui.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/maintenance/' . $id);
    }

    public function updatePriority(Request $request): Response
    {
        $id = (int)$request->param('id');
        $priority = (string)$request->post('priority');
        $notes = (string)$request->post('notes', '');

        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $this->maintService->updatePriority($id, $priority, (int)($user['id'] ?? 1), $notes, $actor);
            Session::flash('success', 'Prioritas laporan perbaikan berhasil diperbarui.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/maintenance/' . $id);
    }

    public function addUpdate(Request $request): Response
    {
        $id = (int)$request->param('id');
        $notes = (string)$request->post('notes');
        $isInternal = (bool)$request->post('is_internal', false);

        $user = Session::get('user');
        $actor = ($user['name'] ?? 'Admin') . ' (' . ($user['role'] ?? 'admin') . ')';

        try {
            $this->maintService->addUpdate($id, (int)($user['id'] ?? 1), $notes, $isInternal, $actor);
            Session::flash('success', 'Catatan perkembangan berhasil ditambahkan.');
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/maintenance/' . $id);
    }

    public function downloadAttachment(Request $request): Response
    {
        $id = (int)$request->param('id');
        $attachment = $this->maintService->getAttachmentById($id);

        if (!$attachment) {
            return new Response('File lampiran tidak ditemukan.', 404);
        }

        $fullPath = dirname(__DIR__, 2) . '/storage/uploads/' . $attachment->file_path;
        if (!file_exists($fullPath)) {
            return new Response('File fisik tidak ditemukan di server storage.', 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fullPath);
        if (PHP_VERSION_ID < 80500) {
            finfo_close($finfo);
        }

        $content = file_get_contents($fullPath);

        return new Response($content, 200, [
            'Content-Type' => $mime ?: $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"',
            'Content-Length' => (string)filesize($fullPath),
        ]);
    }
}
