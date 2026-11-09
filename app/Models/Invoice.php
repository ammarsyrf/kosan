<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Invoice extends Model
{
    public int $id;
    public string $invoice_number;
    public int $contract_id;
    public int $tenant_id;
    public string $period_start;
    public string $period_end;
    public string $issue_date;
    public string $due_date;
    public float $subtotal;
    public float $total_amount;
    public float $paid_amount = 0.00;
    public float $balance;
    public int $is_first_month_manual = 0;
    public string $status;
    public ?string $notes = null;
    public string $created_at;
    public string $updated_at;

    // Joined fields
    public string $room_number = '';
    public string $tenant_name = '';
    public string $tenant_phone = '';
    public string $contract_number = '';
    public array $items = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['subtotal', 'total_amount', 'paid_amount', 'balance'], true)) {
                    $this->$key = (float)$value;
                } elseif (in_array($key, ['id', 'contract_id', 'tenant_id', 'is_first_month_manual'], true)) {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return in_array($this->status, ['unpaid', 'partially_paid'], true);
    }

    public function isOverdue(): bool
    {
        return $this->isUnpaid() && (strtotime($this->due_date) < strtotime(date('Y-m-d')));
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function formattedBalance(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    public function formattedPaid(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }
}

