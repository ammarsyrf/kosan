<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MaintenanceCategory extends Model
{
    public int $id;
    public string $name;
    public ?string $description = null;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'id') {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }
}
