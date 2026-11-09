<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Services\BookingService;
use App\Services\ContractService;

class ContractController extends Controller
{
    private ContractService $contractService;
    private BookingService $bookingService;
    private RoomRepository $roomRepo;
    private TenantRepository $tenantRepo;

    public function __construct(
        ?ContractService $contractService = null,
        ?BookingService $bookingService = null,
        ?RoomRepository $roomRepo = null,
        ?TenantRepository $tenantRepo = null
    ) {
        $this->contractService = $contractService ?? new ContractService();
        $this->bookingService = $bookingService ?? new BookingService();
        $this->roomRepo = $roomRepo ?? new RoomRepository();
        $this->tenantRepo = $tenantRepo ?? new TenantRepository();
    }

    public function index(): Response
    {
        $contracts = $this->contractService->getAllContracts();

        return $this->render('admin/contracts/index', [
            'title' => 'Daftar Kontrak Sewa',
            'contracts' => $contracts,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->param('id');
        $contract = $this->contractService->getContractById($id);

        if (!$contract) {
            Session::flash('error', 'Kontrak tidak ditemukan.');
            return $this->redirect('/admin/contracts');
        }

        $histories = $this->contractService->getHistories($id);
        $availableRooms = $this->roomRepo->getAvailableRooms();

        return $this->render('admin/contracts/show', [
            'title' => "Detail Kontrak #{$contract->contract_number}",
            'contract' => $contract,
            'histories' => $histories,
            'availableRooms' => $availableRooms,
        ]);
    }

    public function create(): Response
    {
        $rooms = $this->roomRepo->getAvailableRooms();
        $tenants = $this->tenantRepo->getAll();

        return $this->render('admin/contracts/create', [
            'title' => 'Buat Kontrak Sewa Langsung',
            'rooms' => $rooms,
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request): Response
    {
        $durationType = (string)$request->post('duration_type', 'rolling');
        $startDate = (string)$request->post('start_date', '');

        // If rolling monthly (Indonesian kos standard), auto-generate 1-month rolling end_date if omitted
        if ($durationType === 'rolling' && $startDate && empty($request->post('end_date'))) {
            $months = (int)$request->post('rolling_months', 1);
            $endDate = date('Y-m-d', strtotime("{$startDate} + {$months} months - 1 day"));
            $_POST['end_date'] = $endDate;
        }

        $validated = $this->validate($request, [
            'room_id' => 'required|numeric',
            'tenant_id' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $user = Session::get('user');

        try {
            $notes = (string)$request->post('notes', '');
            if ($durationType === 'rolling') {
                $notes = trim("[Sewa Bulanan Bergulir / Auto-Renew] " . $notes);
            }

            $contractData = [
                'room_id' => (int)$validated['room_id'],
                'tenant_id' => (int)$validated['tenant_id'],
                'start_date' => (string)$validated['start_date'],
                'end_date' => (string)$validated['end_date'],
                'notes' => $notes,
            ];

            $rentPrice = $request->post('rent_price_snapshot');
            if ($rentPrice !== null && $rentPrice !== '') {
                $contractData['rent_price_snapshot'] = (float)$rentPrice;
            }

            $anchorDay = $request->post('billing_anchor_day');
            if ($anchorDay !== null && $anchorDay !== '') {
                $contractData['billing_anchor_day'] = (int)$anchorDay;
            }

            $contractId = $this->contractService->createDirectContract(
                $contractData,
                $user['id'] ?? null,
                $user['name'] ?? 'Admin'
            );

            Session::flash('success', 'Kontrak sewa berhasil diterbitkan.');
            return $this->redirect("/admin/contracts/{$contractId}");
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            Session::flash('old', $request->all());
            return $this->redirect('/admin/contracts/create');
        }
    }

    public function transferRoom(Request $request): Response
    {
        $contractId = (int)$request->param('id');
        $validated = $this->validate($request, [
            'new_room_id' => 'required|numeric',
            'effective_date' => 'required',
        ]);

        $newPrice = $request->post('new_price') ? (float)$request->post('new_price') : null;
        $reason = (string)$request->post('reason', '');
        $user = Session::get('user');

        try {
            $this->contractService->transferRoom(
                $contractId,
                (int)$validated['new_room_id'],
                (string)$validated['effective_date'],
                $newPrice,
                $reason,
                $user['id'] ?? null,
                $user['name'] ?? 'Admin'
            );

            Session::flash('success', 'Mutasi pindah kamar berhasil diproses. Kamar lama otomatis berstatus tersedia.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/contracts/{$contractId}");
    }

    public function terminate(Request $request): Response
    {
        $contractId = (int)$request->param('id');
        $checkoutDate = (string)$request->post('checkout_date', date('Y-m-d'));
        $reason = (string)$request->post('reason', 'Selesai masa sewa');
        $user = Session::get('user');

        try {
            $this->contractService->terminateContract(
                $contractId,
                $checkoutDate,
                $reason,
                $user['id'] ?? null,
                $user['name'] ?? 'Admin'
            );

            Session::flash('success', 'Pengakhiran sewa berhasil diproses. Kamar otomatis diset menjadi tersedia.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        return $this->redirect("/admin/contracts/{$contractId}");
    }

    public function reviewExtension(Request $request): Response
    {
        $requestId = (int)$request->param('id');
        $approve = (string)$request->post('action') === 'approve';
        $newPrice = $request->post('new_price') ? (float)$request->post('new_price') : null;
        $notes = (string)$request->post('admin_notes', '');
        $user = Session::get('user');

        try {
            $this->contractService->reviewExtension(
                $requestId,
                $approve,
                $newPrice,
                $user['id'] ?? null,
                $notes,
                $user['name'] ?? 'Admin'
            );

            $msg = $approve ? 'Pengajuan perpanjangan berhasil disetujui.' : 'Pengajuan perpanjangan ditolak.';
            Session::flash('success', $msg);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $contractId = (int)$request->post('contract_id', 0);
        return $this->redirect($contractId ? "/admin/contracts/{$contractId}" : '/admin/contracts');
    }

    public function reviewEarlyCheckout(Request $request): Response
    {
        $requestId = (int)$request->param('id');
        $approve = (string)$request->post('action') === 'approve';
        $financialDecision = (string)$request->post('financial_decision', 'no_refund');
        $refundAmount = (float)$request->post('refund_amount', 0);
        $effectiveDate = (string)$request->post('effective_date', '');
        $notes = (string)$request->post('admin_notes', '');
        $user = Session::get('user');

        try {
            $this->contractService->reviewEarlyCheckout(
                $requestId,
                $approve,
                $financialDecision,
                $refundAmount,
                $effectiveDate ?: null,
                $user['id'] ?? null,
                $notes,
                $user['name'] ?? 'Admin'
            );

            $msg = $approve ? 'Pengajuan early checkout berhasil disetujui dan kontrak diakhiri.' : 'Pengajuan early checkout ditolak.';
            Session::flash('success', $msg);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $contractId = (int)$request->post('contract_id', 0);
        return $this->redirect($contractId ? "/admin/contracts/{$contractId}" : '/admin/contracts');
    }
}
