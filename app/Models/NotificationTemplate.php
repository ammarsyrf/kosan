<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class NotificationTemplate extends Model
{
    public int $id;
    public string $code;
    public string $name;
    public string $channel;
    public string $content;
    public ?string $variables = null;
    public int $is_active = 1;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'id' || $key === 'is_active') {
                    $this->$key = (int)$value;
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
