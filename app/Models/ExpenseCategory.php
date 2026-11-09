<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ExpenseCategory extends Model
{
    public int $id;
    public string $name;
    public ?string $description = null;
    public int $is_active = 1;
    public string $created_at;
    public ?string $updated_at = null;

    // Joined / aggregate fields
    public int $expenses_count = 0;
    public float $expenses_total = 0.00;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['id', 'is_active', 'expenses_count'], true)) {
                    $this->$key = (int)$value;
                } elseif ($key === 'expenses_total') {
                    $this->$key = (float)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isActive(): bool
    {
        return $this->is_active === 1;
    }
}
