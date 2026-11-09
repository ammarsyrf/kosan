<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class WhatsAppLog extends Model
{
    public int $id;
    public string $idempotency_key;
    public string $recipient_phone;
    public string $message;
    public string $status;
    public ?string $response_payload = null;
    public int $retry_count = 0;
    public ?string $last_sent_at = null;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'id' || $key === 'retry_count') {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRetrying(): bool
    {
        return $this->status === 'retrying';
    }
}
