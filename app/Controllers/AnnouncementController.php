<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use PDO;

class AnnouncementController extends Controller
{
    public function index(Request $request): Response
    {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT a.*, u.name AS creator_name,
                   (SELECT COUNT(*) FROM `announcement_reads` WHERE `announcement_id` = a.id) AS read_count
            FROM `announcements` a
            JOIN `users` u ON u.id = a.created_by
            ORDER BY a.created_at DESC
        ");
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $this->render('admin/announcements/index', [
            'title' => 'Pengumuman & Broadcast Kost',
            'announcements' => $announcements,
            'user' => Session::get('user'),
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'title' => 'required|max:200',
            'content' => 'required',
        ]);

        $user = Session::get('user');
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO `announcements` (`title`, `content`, `created_by`, `is_active`)
            VALUES (:title, :content, :created_by, :is_active)
        ");
        $stmt->execute([
            'title' => trim((string)$validated['title']),
            'content' => trim((string)$validated['content']),
            'created_by' => $user['id'] ?? 1,
            'is_active' => $request->post('is_active') ? 1 : 0,
        ]);

        Session::flash('success', 'Pengumuman baru berhasil disiarkan ke seluruh anak kost!');
        return $this->redirect('/admin/announcements');
    }

    public function destroy(Request $request): Response
    {
        $id = (int)$request->param('id');
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM `announcements` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);

        Session::flash('success', 'Pengumuman berhasil dihapus.');
        return $this->redirect('/admin/announcements');
    }
}
