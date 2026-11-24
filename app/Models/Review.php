<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = ['highlight' => 'boolean'];

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }
}
