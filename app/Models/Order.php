<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = ['pickup_date' => 'date', 'opened_at' => 'datetime'];

    public const STATUSES = [
        'menunggu'     => 'Menunggu',
        'diproses'     => 'Diproses',
        'siap_diambil' => 'Siap diambil',
        'selesai'      => 'Sudah diambil',
        'dibatalkan'   => 'Dibatalkan',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function rupiah($n): string
    {
        return 'Rp ' . number_format((int) $n, 0, ',', '.');
    }

    public function getIsNewAttribute(): bool
    {
        return $this->opened_at === null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getWaLinkAttribute(): ?string
    {
        $d = preg_replace('/\D/', '', (string) $this->whatsapp);
        if ($d === '') return null;
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        return 'https://wa.me/' . $d;
    }

    public function getPickupLabelAttribute(): string
    {
        return $this->pickup_date ? $this->pickup_date->locale('id')->translatedFormat('l, d F Y') : '-';
    }

    public function getHasCustomAttribute(): bool
    {
        return $this->items->contains(fn ($i) => $i->price === null);
    }

    public function getSummaryAttribute(): string
    {
        if ($this->items->isEmpty()) {
            return $this->flavors ?: ($this->product_type ?: '-');
        }
        return $this->items->map(fn ($i) => $i->qty . 'x ' . $i->name)->implode(', ');
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->isEmpty() ? 0 : (int) $this->items->sum('qty');
    }
}
