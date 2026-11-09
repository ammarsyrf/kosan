<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    public int $id;
    public string $payment_number;
    public int $tenant_id;
    public float $amount;
    public string $payment_method;
    public string $payment_date;
    public string $status;
    public ?string $rejection_reason = null;
    public ?int $verified_by = null;
    public ?string $verified_at = null;
    public ?string $notes = null;
    public string $created_at;
    public string $updated_at;

    // Joined fields
    public string $tenant_name = '';
    public string $tenant_phone = '';
    public ?string $proof_file = null;
    public array $allocations = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'amount') {
                    $this->$key = (float)$value;
                } elseif (in_array($key, ['id', 'tenant_id', 'verified_by'], true)) {
                    $this->$key = $value !== null ? (int)$value : null;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

