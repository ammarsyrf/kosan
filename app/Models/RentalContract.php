<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class RentalContract extends Model
{
    public int $id;
    public string $contract_number;
    public int $tenant_id;
    public int $room_id;
    public string $start_date;
    public string $end_date;
    public float $rent_price_snapshot;
    public int $billing_anchor_day;
    public string $status;
    public ?string $notes;
    public string $created_at;
    public string $updated_at;

    // Joined fields
    public string $room_number = '';
    public string $room_name = '';
    public string $tenant_name = '';
    public string $tenant_phone = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'rent_price_snapshot') {
                    $this->$key = (float)$value;
                } elseif ($key === 'billing_anchor_day') {
                    $this->$key = (int)$value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function formattedPrice(): string
    {
        return 'Rp ' . number_format($this->rent_price_snapshot, 0, ',', '.');
    }

    public function getDueDateForMonth(int $year, int $month): string
    {
        return self::calculateDueDateForMonth($this->billing_anchor_day, $year, $month);
    }

    public static function calculateDueDateForMonth(int $anchorDay, int $year, int $month): string
    {
        $daysInMonth = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $day = min($anchorDay, $daysInMonth);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
