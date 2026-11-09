<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ContractExtensionRequest extends Model
{
    public int $id;
    public int $contract_id;
    public int $requested_months;
    public string $proposed_start_date;
    public string $proposed_end_date;
    public string $status;
    public ?string $admin_notes;
    public ?int $reviewed_by;
    public ?string $reviewed_at;
    public string $created_at;

    // Joined fields
    public string $contract_number = '';
    public string $room_number = '';
    public string $tenant_name = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'requested_months') {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}

