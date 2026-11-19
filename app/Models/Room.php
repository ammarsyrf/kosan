<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Room extends Model
{
    public int $id;
    public string $room_number;
    public int $floor = 1;
    public string $name;
    public string $size = '3x4 m²';
    public ?string $description;
    public float $current_price;
    public ?string $photo_url = null;
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

    public function mainPhotoUrl(): string
    {
        if (!empty($this->photo_url)) {
            return url($this->photo_url);
        }
        $rNum = $this->room_number ?? '101';
        $local = BASE_PATH . "/public/images/rooms/room-{$rNum}.jpg";
        if (file_exists($local)) {
            return url("images/rooms/room-{$rNum}.jpg");
        }
        return 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80';
    }

    /**
     * Multi-angle gallery photos for the room.
     * @return array<int, array{url: string, title: string, tag: string, icon: string}>
     */
    public function galleryPhotos(): array
    {
        $id = $this->id ?? 1;
        $variant1 = ($id % 2) + 1;
        $variant2 = (($id + 1) % 2) + 1;
        $isFloor2 = ($this->floor ?? 1) === 2;

        return [
            [
                'url' => $this->mainPhotoUrl(),
                'title' => 'Tampak Utama & Kasur Springbed',
                'tag' => 'Kasur & Utama',
                'icon' => 'bed',
            ],
            [
                'url' => url("images/rooms/angles/meja-belajar-{$variant1}.jpg"),
                'title' => 'Sudut Meja Belajar & Kerja',
                'tag' => 'Meja Belajar',
                'icon' => 'laptop',
            ],
            [
                'url' => url("images/rooms/angles/km-dalam-{$variant1}.jpg"),
                'title' => 'Kamar Mandi Dalam & Sanitasi',
                'tag' => 'KM Dalam',
                'icon' => 'bath',
            ],
            [
                'url' => $isFloor2 
                    ? url('images/rooms/angles/balkon-jendela-1.jpg') 
                    : url("images/rooms/angles/lemari-sudut-{$variant2}.jpg"),
                'title' => $isFloor2 ? 'Area Jendela & Sirkulasi Udara' : 'Area Lemari Pakaian & Pintu Masuk',
                'tag' => $isFloor2 ? 'Jendela & View' : 'Lemari & Pintu',
                'icon' => $isFloor2 ? 'sun' : 'door-closed',
            ],
        ];
    }
}

