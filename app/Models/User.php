<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public ?string $phone;
    public string $status;
    public string $role;
    public array $permissions = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function isOwner(): bool
    {
        return strtolower($this->role ?? '') === 'owner';
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin';
    }

    public function isTenant(): bool
    {
        return strtolower($this->role ?? '') === 'tenant';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return in_array($permission, $this->permissions, true);
    }
}

