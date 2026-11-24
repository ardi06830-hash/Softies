<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_premium' => 'boolean'];

    public const CATEGORIES = [
        'cookie'  => ['label' => 'Soft Cookies',   'emoji' => '🍪'],
        'brownie' => ['label' => 'Brownies Bites', 'emoji' => '🍫'],
        'scoop'   => ['label' => 'Scoop Cookies',  'emoji' => '🍨'],
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function getCategoryEmojiAttribute(): string
    {
        return self::CATEGORIES[$this->category]['emoji'] ?? '🍪';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? $this->category;
    }
}
