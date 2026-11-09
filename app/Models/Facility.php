<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Facility extends Model
{
    public int $id;
    public string $name;
    public string $icon;
    public ?string $description;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

