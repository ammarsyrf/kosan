<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\ContractExtensionRequest;
use App\Models\ContractHistory;
use App\Models\EarlyCheckoutRequest;
use App\Models\RentalContract;
use PDO;

class ContractRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT c.*, r.room_number, r.name AS room_name, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            ORDER BY c.created_at DESC
        ";

        $rows = $this->db->query($sql)->fetchAll();
        return array_map(function ($row) {
            $contract = new RentalContract($row);
            $contract->room_number = $row['room_number'];
            $contract->room_name = $row['room_name'];
            $contract->tenant_name = $row['tenant_name'];
            $contract->tenant_phone = $row['tenant_phone'] ?? '';
            return $contract;
        }, $rows);
    }

    public function findById(int $id): ?RentalContract
    {
        $stmt = $this->db->prepare("
            SELECT c.*, r.room_number, r.name AS room_name, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $contract = new RentalContract($row);
        $contract->room_number = $row['room_number'];
        $contract->room_name = $row['room_name'];
        $contract->tenant_name = $row['tenant_name'];
        $contract->tenant_phone = $row['tenant_phone'] ?? '';
        return $contract;
    }

    public function getByTenantId(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, r.room_number, r.name AS room_name, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE c.tenant_id = :tenant_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute(['tenant_id' => $tenantId]);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $contract = new RentalContract($row);
            $contract->room_number = $row['room_number'];
            $contract->room_name = $row['room_name'];
            $contract->tenant_name = $row['tenant_name'];
            $contract->tenant_phone = $row['tenant_phone'] ?? '';
            return $contract;
        }, $rows);
    }

    public function getActiveContractByTenantId(int $tenantId): ?RentalContract
    {
        $stmt = $this->db->prepare("
            SELECT c.*, r.room_number, r.name AS room_name, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE c.tenant_id = :tenant_id AND c.status = 'active'
            ORDER BY c.id DESC
            LIMIT 1
        ");
        $stmt->execute(['tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $contract = new RentalContract($row);
        $contract->room_number = $row['room_number'];
        $contract->room_name = $row['room_name'];
        $contract->tenant_name = $row['tenant_name'];
        $contract->tenant_phone = $row['tenant_phone'] ?? '';
        return $contract;
    }

    public function getLatestContractByTenantId(int $tenantId): ?RentalContract
    {
        $stmt = $this->db->prepare("
            SELECT c.*, r.room_number, r.name AS room_name, u.name AS tenant_name, u.phone AS tenant_phone
            FROM `rental_contracts` c
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE c.tenant_id = :tenant_id
            ORDER BY c.id DESC
            LIMIT 1
        ");
        $stmt->execute(['tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $contract = new RentalContract($row);
        $contract->room_number = $row['room_number'];
        $contract->room_name = $row['room_name'];
        $contract->tenant_name = $row['tenant_name'];
        $contract->tenant_phone = $row['tenant_phone'] ?? '';
        return $contract;
    }

    public function createContract(array $data, ?int $createdBy = null): int
    {
        $contractNumber = $this->generateContractNumber();

        $stmt = $this->db->prepare("
            INSERT INTO `rental_contracts` (
                `contract_number`, `tenant_id`, `room_id`, `start_date`, `end_date`,
                `rent_price_snapshot`, `billing_anchor_day`, `status`, `notes`
            ) VALUES (
                :contract_number, :tenant_id, :room_id, :start_date, :end_date,
                :rent_price_snapshot, :billing_anchor_day, :status, :notes
            )
        ");

        $stmt->execute([
            'contract_number' => $contractNumber,
            'tenant_id' => $data['tenant_id'],
            'room_id' => $data['room_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'rent_price_snapshot' => $data['rent_price_snapshot'],
            'billing_anchor_day' => (int)$data['billing_anchor_day'],
            'status' => 'active',
            'notes' => $data['notes'] ?? null,
        ]);

        $contractId = (int)$this->db->lastInsertId();

        // Update room status to occupied
        $stmtRoom = $this->db->prepare("UPDATE `rooms` SET `status` = 'occupied' WHERE `id` = :id");
        $stmtRoom->execute(['id' => $data['room_id']]);

        // Record initial contract history
        $this->addHistory([
            'contract_id' => $contractId,
            'action' => 'CREATED',
            'old_end_date' => null,
            'new_end_date' => $data['end_date'],
            'old_rent_price' => null,
            'new_rent_price' => $data['rent_price_snapshot'],
            'notes' => 'Pembuatan kontrak sewa baru',
            'created_by' => $createdBy,
        ]);

        return $contractId;
    }

    public function addHistory(array $data): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO `contract_histories` (
                `contract_id`, `action`, `old_end_date`, `new_end_date`,
                `old_rent_price`, `new_rent_price`, `notes`, `created_by`
            ) VALUES (
                :contract_id, :action, :old_end_date, :new_end_date,
                :old_rent_price, :new_rent_price, :notes, :created_by
            )
        ");

        $stmt->execute([
            'contract_id' => $data['contract_id'],
            'action' => $data['action'],
            'old_end_date' => $data['old_end_date'] ?? null,
            'new_end_date' => $data['new_end_date'] ?? null,
            'old_rent_price' => $data['old_rent_price'] ?? null,
            'new_rent_price' => $data['new_rent_price'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    public function getHistories(int $contractId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `contract_histories`
            WHERE `contract_id` = :cid
            ORDER BY `created_at` DESC
        ");
        $stmt->execute(['cid' => $contractId]);
        return array_map(fn($r) => new ContractHistory($r), $stmt->fetchAll());
    }

    public function createExtensionRequest(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `contract_extension_requests` (
                `contract_id`, `requested_months`, `proposed_start_date`, `proposed_end_date`, `status`
            ) VALUES (
                :contract_id, :requested_months, :proposed_start_date, :proposed_end_date, 'pending'
            )
        ");
        $stmt->execute([
            'contract_id' => $data['contract_id'],
            'requested_months' => $data['requested_months'],
            'proposed_start_date' => $data['proposed_start_date'],
            'proposed_end_date' => $data['proposed_end_date'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getExtensionRequests(): array
    {
        $sql = "
            SELECT er.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `contract_extension_requests` er
            JOIN `rental_contracts` c ON c.id = er.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            ORDER BY er.created_at DESC
        ";
        $rows = $this->db->query($sql)->fetchAll();
        return array_map(function ($r) {
            $req = new ContractExtensionRequest($r);
            $req->contract_number = $r['contract_number'];
            $req->room_number = $r['room_number'];
            $req->tenant_name = $r['tenant_name'];
            return $req;
        }, $rows);
    }

    public function findExtensionRequestById(int $id): ?ContractExtensionRequest
    {
        $stmt = $this->db->prepare("
            SELECT er.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `contract_extension_requests` er
            JOIN `rental_contracts` c ON c.id = er.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE er.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch();
        if (!$r) {
            return null;
        }

        $req = new ContractExtensionRequest($r);
        $req->contract_number = $r['contract_number'];
        $req->room_number = $r['room_number'];
        $req->tenant_name = $r['tenant_name'];
        return $req;
    }

    public function createEarlyCheckoutRequest(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `early_checkout_requests` (
                `contract_id`, `requested_checkout_date`, `reason`, `status`
            ) VALUES (
                :contract_id, :requested_checkout_date, :reason, 'pending'
            )
        ");
        $stmt->execute([
            'contract_id' => $data['contract_id'],
            'requested_checkout_date' => $data['requested_checkout_date'],
            'reason' => $data['reason'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getEarlyCheckoutRequests(): array
    {
        $sql = "
            SELECT ecr.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `early_checkout_requests` ecr
            JOIN `rental_contracts` c ON c.id = ecr.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            ORDER BY ecr.created_at DESC
        ";
        $rows = $this->db->query($sql)->fetchAll();
        return array_map(function ($r) {
            $req = new EarlyCheckoutRequest($r);
            $req->contract_number = $r['contract_number'];
            $req->room_number = $r['room_number'];
            $req->tenant_name = $r['tenant_name'];
            return $req;
        }, $rows);
    }

    public function findEarlyCheckoutRequestById(int $id): ?EarlyCheckoutRequest
    {
        $stmt = $this->db->prepare("
            SELECT ecr.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `early_checkout_requests` ecr
            JOIN `rental_contracts` c ON c.id = ecr.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE ecr.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch();
        if (!$r) {
            return null;
        }

        $req = new EarlyCheckoutRequest($r);
        $req->contract_number = $r['contract_number'];
        $req->room_number = $r['room_number'];
        $req->tenant_name = $r['tenant_name'];
        return $req;
    }

    public function getPendingExtensionByContractId(int $contractId): ?ContractExtensionRequest
    {
        $stmt = $this->db->prepare("
            SELECT er.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `contract_extension_requests` er
            JOIN `rental_contracts` c ON c.id = er.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE er.contract_id = :contract_id AND er.status = 'pending'
            ORDER BY er.created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['contract_id' => $contractId]);
        $r = $stmt->fetch();
        if (!$r) {
            return null;
        }

        $req = new ContractExtensionRequest($r);
        $req->contract_number = $r['contract_number'];
        $req->room_number = $r['room_number'];
        $req->tenant_name = $r['tenant_name'];
        return $req;
    }

    public function getPendingEarlyCheckoutByContractId(int $contractId): ?EarlyCheckoutRequest
    {
        $stmt = $this->db->prepare("
            SELECT ecr.*, c.contract_number, r.room_number, u.name AS tenant_name
            FROM `early_checkout_requests` ecr
            JOIN `rental_contracts` c ON c.id = ecr.contract_id
            JOIN `rooms` r ON r.id = c.room_id
            JOIN `tenants` t ON t.id = c.tenant_id
            JOIN `users` u ON u.id = t.user_id
            WHERE ecr.contract_id = :contract_id AND ecr.status = 'pending'
            ORDER BY ecr.created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['contract_id' => $contractId]);
        $r = $stmt->fetch();
        if (!$r) {
            return null;
        }

        $req = new EarlyCheckoutRequest($r);
        $req->contract_number = $r['contract_number'];
        $req->room_number = $r['room_number'];
        $req->tenant_name = $r['tenant_name'];
        return $req;
    }

    private function generateContractNumber(): string
    {
        $prefix = 'KTR-' . date('Ym') . '-';
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        return $prefix . $random;
    }
}

