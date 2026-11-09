<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\FacilityRepository;

class FacilityController extends Controller
{
    private FacilityRepository $facilityRepo;

    public function __construct(?FacilityRepository $facilityRepo = null)
    {
        $this->facilityRepo = $facilityRepo ?? new FacilityRepository();
    }

    public function index(): Response
    {
        $facilities = $this->facilityRepo->getAll();

        return $this->render('admin/facilities/index', [
            'title' => 'Daftar Fasilitas Kamar',
            'facilities' => $facilities,
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|max:100',
        ]);

        try {
            $this->facilityRepo->create([
                'name' => (string)$validated['name'],
                'icon' => (string)$request->post('icon', 'check'),
                'description' => (string)$request->post('description', ''),
            ]);

            Session::flash('success', "Fasilitas {$validated['name']} berhasil ditambahkan.");
        } catch (\Throwable $e) {
            Session::flash('error', "Gagal menambah fasilitas: " . $e->getMessage());
        }

        return $this->redirect('/admin/facilities');
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $validated = $this->validate($request, [
            'name' => 'required|max:100',
        ]);

        try {
            $this->facilityRepo->update($id, [
                'name' => (string)$validated['name'],
                'icon' => (string)$request->post('icon', 'check'),
                'description' => (string)$request->post('description', ''),
            ]);

            Session::flash('success', "Fasilitas {$validated['name']} berhasil diubah.");
        } catch (\Throwable $e) {
            Session::flash('error', "Gagal memperbarui fasilitas: " . $e->getMessage());
        }

        return $this->redirect('/admin/facilities');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->param('id');

        try {
            $this->facilityRepo->delete($id);
            Session::flash('success', 'Fasilitas berhasil dihapus.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal menghapus fasilitas: ' . $e->getMessage());
        }

        return $this->redirect('/admin/facilities');
    }
}

