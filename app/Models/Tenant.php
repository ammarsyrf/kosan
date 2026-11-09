<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Tenant extends Model
{
    public int $id;
    public int $user_id;
    public ?string $id_card_number;
    public ?string $emergency_contact_name;
    public ?string $emergency_contact_phone;
    public ?string $occupation;
    public ?string $notes;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $status = 'pending';
    public ?string $room_number = null;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
