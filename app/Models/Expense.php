<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Expense extends Model
{
    public int $id;
    public int $category_id;
    public string $transaction_date;
    public float $amount;
    public string $description;
    public ?string $vendor = null;
    public string $payment_method = 'cash';
    public ?string $receipt_path = null;
    public int $created_by;
    public ?string $notes = null;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at = null;

    // Joined fields
    public string $category_name = '';
    public string $creator_name = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['id', 'category_id', 'created_by'], true)) {
                    $this->$key = (int)$value;
                } elseif ($key === 'amount') {
                    $this->$key = (float)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function isDeleted(): bool
    {
        return !empty($this->deleted_at);
    }
}
