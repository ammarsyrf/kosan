<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Booking extends Model
{
    public int $id;
    public int $room_id;
    public int $tenant_id;
    public string $start_date;
    public string $end_date;
    public string $status;
    public int $duration_hours_snapshot;
    public string $expires_at;
    public ?string $notes;
    public string $created_at;
    public string $updated_at;

    // Joined fields
    public string $room_number = '';
    public string $tenant_name = '';
    public string $tenant_phone = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'duration_hours_snapshot') {
                    $this->$key = (int)$value;
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

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->isPending() && strtotime($this->expires_at) < time());
    }
}

