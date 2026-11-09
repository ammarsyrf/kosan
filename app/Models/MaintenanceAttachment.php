<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MaintenanceAttachment extends Model
{
    public int $id;
    public int $request_id;
    public string $file_path;
    public string $file_name;
    public string $mime_type;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, ['id', 'request_id'], true)) {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }
}
