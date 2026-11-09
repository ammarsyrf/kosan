<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class InvoiceItem extends Model
{
    public int $id;
    public int $invoice_id;
    public string $description;
    public float $amount;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'amount') {
                    $this->$key = (float)$value;
                } elseif (in_array($key, ['id', 'invoice_id'], true)) {
                    $this->$key = (int)$value;
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
}

