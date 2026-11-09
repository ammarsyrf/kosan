<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MaintenanceRequest extends Model
{
    public int $id;
    public int $tenant_id;
    public int $room_id;
    public int $category_id;
    public string $title;
    public string $description;
    public string $priority = 'medium'; // low, medium, high, urgent
    public string $status = 'submitted'; // submitted, reviewed, in_progress, waiting, resolved, rejected
    public ?string $resolved_at = null;
    public string $created_at;
    public string $updated_at;

    // Joined fields
    public string $tenant_name = '';
    public string $tenant_phone = '';
    public string $room_number = '';
    public string $category_name = '';
    public array $attachments = [];
    public array $updates = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['id', 'tenant_id', 'room_id', 'category_id'], true)) {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
