<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MaintenanceUpdate extends Model
{
    public int $id;
    public int $request_id;
    public int $user_id;
    public ?string $status_changed_to = null;
    public string $notes;
    public int $is_internal = 0;
    public string $created_at;

    // Joined fields
    public string $user_name = '';
    public string $user_role = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['id', 'request_id', 'user_id', 'is_internal'], true)) {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isInternal(): bool
    {
        return $this->is_internal === 1;
    }
}
