<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Room extends Model
{
    public int $id;
    public string $room_number;
    public string $name;
    public ?string $description;
    public float $current_price;
    public string $status;
    public ?string $notes;
    public string $created_at;
    public string $updated_at;
    public array $facilities = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'current_price') {
                    $this->$key = (float)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    public function formattedPrice(): string
    {
        return 'Rp ' . number_format($this->current_price, 0, ',', '.');
    }
}

