<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;
use App\Models\ContractExtensionRequest;
use App\Models\EarlyCheckoutRequest;
use App\Models\RentalContract;
use App\Repositories\BookingRepository;
use App\Repositories\ContractRepository;
use PDO;
use RuntimeException;

class ContractService
{
    private ContractRepository $contractRepo;
    private BookingRepository $bookingRepo;

    public function __construct(
        ?ContractRepository $contractRepo = null,
        ?BookingRepository $bookingRepo = null
    ) {
        $this->contractRepo = $contractRepo ?? new ContractRepository();
        $this->bookingRepo = $bookingRepo ?? new BookingRepository();
    }

    public function getAllContracts(): array
    {
        return $this->contractRepo->getAll();
    }

    public function getContractById(int $id): ?RentalContract
    {
        return $this->contractRepo->findById($id);
    }

    public function getTenantContracts(int $tenantId): array
    {
        return $this->contractRepo->getByTenantId($tenantId);
    }

    public function getActiveContractByTenantId(int $tenantId): ?RentalContract
    {
        return $this->contractRepo->getActiveContractByTenantId($tenantId);
    }

    public function getHistories(int $contractId): array
    {
        return $this->contractRepo->getHistories($contractId);
    }

    public function createDirectContract(array $data, ?int $adminId = null, ?string $actor = 'Admin'): int
    {
        return Database::transaction(function (PDO $pdo) use ($data, $adminId, $actor) {
            $roomId = (int)$data['room_id'];
            $tenantId = (int)$data['tenant_id'];
            $startDate = (string)$data['start_date'];
            $endDate = (string)$data['end_date'];

            if ($startDate >= $endDate) {
                throw new RuntimeException("Tanggal mulai sewa harus lebih awal daripada tanggal berakhir.");
            }

            // Lock room
            $stmtLock = $pdo->prepare("SELECT `id`, `current_price`, `status` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $roomId]);
            $room = $stmtLock->fetch();

            if (!$room) {
                throw new RuntimeException("Kamar tidak ditemukan.");
            }

            // Check availability
            $isAvail = $this->bookingRepo->isRoomAvailable($roomId, $startDate, $endDate);
            if (!$isAvail) {
                throw new RuntimeException("Kamar tidak tersedia pada periode tersebut karena sudah terisi atau memiliki jadwal sewa.");
            }

            $priceSnapshot = isset($data['rent_price_snapshot']) && (float)$data['rent_price_snapshot'] > 0
                ? (float)$data['rent_price_snapshot']
                : (float)$room['current_price'];

            // Anchor day from start_date
            $startDay = (int)date('j', strtotime($startDate));
            $anchorDay = isset($data['billing_anchor_day']) ? (int)$data['billing_anchor_day'] : $startDay;

            $contractId = $this->contractRepo->createContract([
                'tenant_id' => $tenantId,
                'room_id' => $roomId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'rent_price_snapshot' => $priceSnapshot,
                'billing_anchor_day' => $anchorDay,
                'notes' => $data['notes'] ?? null,
            ], $adminId);

            Logger::audit((string)$actor, 'CREATE_DIRECT_CONTRACT', 'rental_contracts', $contractId, [
                'tenant_id' => $tenantId,
                'room_id' => $roomId,
                'price' => $priceSnapshot,
                'anchor_day' => $anchorDay,
            ]);

            return $contractId;
        });
    }

    public function requestExtension(int $contractId, int $months, ?string $actor = 'Tenant'): int
    {
        $contract = $this->contractRepo->findById($contractId);
        if (!$contract) {
            throw new RuntimeException("Kontrak tidak ditemukan.");
        }

        if (!$contract->isActive()) {
            throw new RuntimeException("Perpanjangan hanya dapat diajukan untuk kontrak yang sedang aktif.");
        }

        $proposedStart = date('Y-m-d', strtotime($contract->end_date . ' + 1 day'));
        $proposedEnd = date('Y-m-d', strtotime($proposedStart . " + {$months} months - 1 day"));

        $reqId = $this->contractRepo->createExtensionRequest([
            'contract_id' => $contractId,
            'requested_months' => $months,
            'proposed_start_date' => $proposedStart,
            'proposed_end_date' => $proposedEnd,
        ]);

        Logger::audit((string)$actor, 'REQUEST_EXTENSION', 'contract_extension_requests', $reqId, [
            'contract_id' => $contractId,
            'months' => $months,
        ]);

        return $reqId;
    }

    public function reviewExtension(
        int $requestId,
        bool $approve,
        ?float $newPrice = null,
        ?int $reviewedBy = null,
        ?string $adminNotes = null,
        ?string $actor = 'Admin'
    ): bool {
        return Database::transaction(function (PDO $pdo) use ($requestId, $approve, $newPrice, $reviewedBy, $adminNotes, $actor) {
            $req = $this->contractRepo->findExtensionRequestById($requestId);
            if (!$req || !$req->isPending()) {
                throw new RuntimeException("Permintaan perpanjangan tidak ditemukan atau sudah diproses.");
            }

            $contract = $this->contractRepo->findById($req->contract_id);
            if (!$contract) {
                throw new RuntimeException("Kontrak terkait tidak ditemukan.");
            }

            if (!$approve) {
                $stmt = $pdo->prepare("
                    UPDATE `contract_extension_requests`
                    SET `status` = 'rejected', `admin_notes` = :notes, `reviewed_by` = :by, `reviewed_at` = NOW()
                    WHERE `id` = :id
                ");
                $stmt->execute(['id' => $requestId, 'notes' => $adminNotes, 'by' => $reviewedBy]);

                Logger::audit((string)$actor, 'REJECT_EXTENSION', 'contract_extension_requests', $requestId);
                return true;
            }

            // Lock room row
            $stmtLock = $pdo->prepare("SELECT `id` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtLock->execute(['id' => $contract->room_id]);

            // Check availability for the proposed extension window
            $isAvail = $this->bookingRepo->isRoomAvailable($contract->room_id, $req->proposed_start_date, $req->proposed_end_date);
            if (!$isAvail) {
                throw new RuntimeException("Kamar sudah terisi atau memiliki reservasi lain pada periode perpanjangan yang diajukan.");
            }

            $finalPrice = $newPrice !== null && $newPrice > 0 ? $newPrice : $contract->rent_price_snapshot;

            // Update rental contract
            $stmtUpdate = $pdo->prepare("
                UPDATE `rental_contracts`
                SET `end_date` = :new_end, `rent_price_snapshot` = :new_price, `status` = 'active'
                WHERE `id` = :id
            ");
            $stmtUpdate->execute([
                'id' => $contract->id,
                'new_end' => $req->proposed_end_date,
                'new_price' => $finalPrice,
            ]);

            // Update request
            $stmtReq = $pdo->prepare("
                UPDATE `contract_extension_requests`
                SET `status` = 'approved', `admin_notes` = :notes, `reviewed_by` = :by, `reviewed_at` = NOW()
                WHERE `id` = :id
            ");
            $stmtReq->execute(['id' => $requestId, 'notes' => $adminNotes, 'by' => $reviewedBy]);

            // Add history
            $this->contractRepo->addHistory([
                'contract_id' => $contract->id,
                'action' => 'EXTENDED',
                'old_end_date' => $contract->end_date,
                'new_end_date' => $req->proposed_end_date,
                'old_rent_price' => $contract->rent_price_snapshot,
                'new_rent_price' => $finalPrice,
                'notes' => $adminNotes ?? "Perpanjangan {$req->requested_months} bulan disetujui",
                'created_by' => $reviewedBy,
            ]);

            Logger::audit((string)$actor, 'APPROVE_EXTENSION', 'rental_contracts', $contract->id, [
                'new_end_date' => $req->proposed_end_date,
                'new_price' => $finalPrice,
            ]);

            return true;
        });
    }

    public function requestEarlyCheckout(int $contractId, string $requestedDate, ?string $reason = null, ?string $actor = 'Tenant'): int
    {
        $contract = $this->contractRepo->findById($contractId);
        if (!$contract) {
            throw new RuntimeException("Kontrak tidak ditemukan.");
        }

        if (!$contract->isActive()) {
            throw new RuntimeException("Pengajuan checkout hanya dapat dilakukan untuk kontrak yang sedang aktif.");
        }

        if ($requestedDate < $contract->start_date || $requestedDate > $contract->end_date) {
            throw new RuntimeException("Tanggal checkout harus berada dalam rentang periode kontrak aktif.");
        }

        $reqId = $this->contractRepo->createEarlyCheckoutRequest([
            'contract_id' => $contractId,
            'requested_checkout_date' => $requestedDate,
            'reason' => $reason,
        ]);

        Logger::audit((string)$actor, 'REQUEST_EARLY_CHECKOUT', 'early_checkout_requests', $reqId, [
            'contract_id' => $contractId,
            'requested_date' => $requestedDate,
        ]);

        return $reqId;
    }

    public function reviewEarlyCheckout(
        int $requestId,
        bool $approve,
        string $financialDecision = 'no_refund',
        float $refundAmount = 0.00,
        ?string $effectiveDate = null,
        ?int $reviewedBy = null,
        ?string $adminNotes = null,
        ?string $actor = 'Admin'
    ): bool {
        return Database::transaction(function (PDO $pdo) use (
            $requestId, $approve, $financialDecision, $refundAmount, $effectiveDate, $reviewedBy, $adminNotes, $actor
        ) {
            $req = $this->contractRepo->findEarlyCheckoutRequestById($requestId);
            if (!$req || !$req->isPending()) {
                throw new RuntimeException("Permintaan checkout tidak ditemukan atau sudah pernah ditinjau.");
            }

            $contract = $this->contractRepo->findById($req->contract_id);
            if (!$contract) {
                throw new RuntimeException("Kontrak tidak ditemukan.");
            }

            if (!$approve) {
                $stmt = $pdo->prepare("
                    UPDATE `early_checkout_requests`
                    SET `status` = 'rejected', `admin_notes` = :notes, `reviewed_by` = :by, `reviewed_at` = NOW()
                    WHERE `id` = :id
                ");
                $stmt->execute(['id' => $requestId, 'notes' => $adminNotes, 'by' => $reviewedBy]);

                Logger::audit((string)$actor, 'REJECT_EARLY_CHECKOUT', 'early_checkout_requests', $requestId);
                return true;
            }

            $effDate = $effectiveDate ?: $req->requested_checkout_date;

            // Update request
            $stmtReq = $pdo->prepare("
                UPDATE `early_checkout_requests`
                SET `status` = 'approved',
                    `financial_decision` = ?,
                    `refund_amount` = ?,
                    `effective_checkout_date` = ?,
                    `admin_notes` = ?,
                    `reviewed_by` = ?,
                    `reviewed_at` = NOW()
                WHERE `id` = ?
            ");
            $stmtReq->execute([
                $financialDecision,
                $refundAmount,
                $effDate,
                $adminNotes,
                $reviewedBy,
                $requestId,
            ]);

            // Update contract status to terminated and shorten end_date
            $stmtContract = $pdo->prepare("
                UPDATE `rental_contracts`
                SET `status` = 'terminated', `end_date` = :eff
                WHERE `id` = :id
            ");
            $stmtContract->execute(['id' => $contract->id, 'eff' => $effDate]);

            // If effective date has passed or is today, free the room
            if ($effDate <= date('Y-m-d')) {
                $stmtRoom = $pdo->prepare("UPDATE `rooms` SET `status` = 'available' WHERE `id` = :id");
                $stmtRoom->execute(['id' => $contract->room_id]);
            }

            // Log to history
            $this->contractRepo->addHistory([
                'contract_id' => $contract->id,
                'action' => 'EARLY_CHECKOUT',
                'old_end_date' => $contract->end_date,
                'new_end_date' => $effDate,
                'old_rent_price' => $contract->rent_price_snapshot,
                'new_rent_price' => $contract->rent_price_snapshot,
                'notes' => "Checkout lebih awal disetujui. Keputusan finansial: {$financialDecision}, Nominal: {$refundAmount}. {$adminNotes}",
                'created_by' => $reviewedBy,
            ]);

            Logger::audit((string)$actor, 'APPROVE_EARLY_CHECKOUT', 'rental_contracts', $contract->id, [
                'effective_date' => $effDate,
                'financial_decision' => $financialDecision,
                'refund_amount' => $refundAmount,
            ]);

            return true;
        });
    }

    public function transferRoom(
        int $contractId,
        int $newRoomId,
        string $effectiveDate,
        ?float $newPrice = null,
        ?string $reason = null,
        ?int $adminId = null,
        ?string $actor = 'Admin'
    ): bool {
        return Database::transaction(function (PDO $pdo) use ($contractId, $newRoomId, $effectiveDate, $newPrice, $reason, $adminId, $actor) {
            $contract = $this->contractRepo->findById($contractId);
            if (!$contract || !$contract->isActive()) {
                throw new RuntimeException("Kontrak tidak ditemukan atau tidak dalam status aktif.");
            }

            if ($contract->room_id === $newRoomId) {
                throw new RuntimeException("Kamar baru tidak boleh sama dengan kamar saat ini.");
            }

            // Lock and inspect new room
            $stmtNew = $pdo->prepare("SELECT `id`, `room_number`, `current_price`, `status` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtNew->execute(['id' => $newRoomId]);
            $newRoom = $stmtNew->fetch(PDO::FETCH_ASSOC);

            if (!$newRoom) {
                throw new RuntimeException("Kamar tujuan tidak ditemukan.");
            }

            if ($newRoom['status'] !== 'available') {
                throw new RuntimeException("Kamar tujuan (Kamar {$newRoom['room_number']}) sedang tidak tersedia (status: {$newRoom['status']}).");
            }

            // Lock old room
            $stmtOld = $pdo->prepare("SELECT `id`, `room_number` FROM `rooms` WHERE `id` = :id FOR UPDATE");
            $stmtOld->execute(['id' => $contract->room_id]);
            $oldRoom = $stmtOld->fetch(PDO::FETCH_ASSOC);
            $oldRoomNumber = $oldRoom['room_number'] ?? (string)$contract->room_id;

            // Determine new price
            $finalPrice = ($newPrice !== null && $newPrice > 0)
                ? (float)$newPrice
                : (float)$newRoom['current_price'];

            // 1. Set old room to available
            $stmtFreeOld = $pdo->prepare("UPDATE `rooms` SET `status` = 'available' WHERE `id` = :id");
            $stmtFreeOld->execute(['id' => $contract->room_id]);

            // 2. Set new room to occupied
            $stmtOccupyNew = $pdo->prepare("UPDATE `rooms` SET `status` = 'occupied' WHERE `id` = :id");
            $stmtOccupyNew->execute(['id' => $newRoomId]);

            // 3. Update contract room_id and rent_price_snapshot
            $auditNote = "Mutasi kamar efektif per {$effectiveDate}: dari Kamar {$oldRoomNumber} ke Kamar {$newRoom['room_number']}. " . ($reason ? "Alasan: {$reason}." : "");
            $newContractNotes = trim(($contract->notes ? $contract->notes . "\n" : "") . "[{$auditNote}]");

            $stmtUpdateContract = $pdo->prepare("
                UPDATE `rental_contracts`
                SET `room_id` = :new_room_id,
                    `rent_price_snapshot` = :new_price,
                    `notes` = :notes,
                    `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmtUpdateContract->execute([
                'new_room_id' => $newRoomId,
                'new_price' => $finalPrice,
                'notes' => $newContractNotes,
                'id' => $contract->id,
            ]);

            // 4. Record history
            $this->contractRepo->addHistory([
                'contract_id' => $contract->id,
                'action' => 'ROOM_TRANSFER',
                'old_end_date' => $contract->end_date,
                'new_end_date' => $contract->end_date,
                'old_rent_price' => $contract->rent_price_snapshot,
                'new_rent_price' => $finalPrice,
                'notes' => $auditNote,
                'created_by' => $adminId,
            ]);

            Logger::audit((string)$actor, 'ROOM_TRANSFER', 'rental_contracts', $contract->id, [
                'old_room_id' => $contract->room_id,
                'new_room_id' => $newRoomId,
                'effective_date' => $effectiveDate,
                'old_price' => $contract->rent_price_snapshot,
                'new_price' => $finalPrice,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    public function terminateContract(
        int $contractId,
        string $checkoutDate,
        string $reason = '',
        ?int $adminId = null,
        string $actor = 'Admin'
    ): bool {
        return Database::transaction(function (PDO $pdo) use ($contractId, $checkoutDate, $reason, $adminId, $actor) {
            $contract = $this->contractRepo->findById($contractId);
            if (!$contract || !$contract->isActive()) {
                throw new RuntimeException("Kontrak tidak ditemukan atau sudah tidak dalam status aktif.");
            }

            // Update contract status to terminated and shorten end_date
            $stmt = $pdo->prepare("
                UPDATE `rental_contracts`
                SET `status` = 'terminated', `end_date` = :eff, `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmt->execute(['id' => $contractId, 'eff' => $checkoutDate]);

            // If effective date has passed or is today, free the room
            if ($checkoutDate <= date('Y-m-d')) {
                $stmtRoom = $pdo->prepare("UPDATE `rooms` SET `status` = 'available' WHERE `id` = :id");
                $stmtRoom->execute(['id' => $contract->room_id]);
            }

            $auditNote = "Pengakhiran sewa / checkout selesai per {$checkoutDate}. " . ($reason ? "Alasan: {$reason}." : "");

            $this->contractRepo->addHistory([
                'contract_id' => $contract->id,
                'action' => 'EARLY_CHECKOUT',
                'old_end_date' => $contract->end_date,
                'new_end_date' => $checkoutDate,
                'old_rent_price' => $contract->rent_price_snapshot,
                'new_rent_price' => $contract->rent_price_snapshot,
                'notes' => $auditNote,
                'created_by' => $adminId,
            ]);

            Logger::audit((string)$actor, 'TERMINATE_CONTRACT', 'rental_contracts', $contractId, [
                'checkout_date' => $checkoutDate,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    public function getExtensionRequests(): array
    {
        return $this->contractRepo->getExtensionRequests();
    }

    public function getEarlyCheckoutRequests(): array
    {
        return $this->contractRepo->getEarlyCheckoutRequests();
    }
}
