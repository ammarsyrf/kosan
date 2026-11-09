<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ContractHistory extends Model
{
    public int $id;
    public int $contract_id;
    public string $action;
    public ?string $old_end_date;
    public ?string $new_end_date;
    public ?float $old_rent_price;
    public ?float $new_rent_price;
    public ?string $notes;
    public ?int $created_by;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'old_rent_price' || $key === 'new_rent_price') {
                    $this->$key = $value !== null ? (float)$value : null;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }
}

