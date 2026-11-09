<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class EarlyCheckoutRequest extends Model
{
    public int $id;
    public int $contract_id;
    public string $requested_checkout_date;
    public ?string $effective_checkout_date;
    public ?string $reason;
    public string $financial_decision;
    public float $refund_amount;
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
                if ($key === 'refund_amount') {
                    $this->$key = (float)$value;
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

