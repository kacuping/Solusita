<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'enabled',
        'max_per_day',
        'hours',
        'starts_at',
        'ends_at',
        'active',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'active' => 'boolean',
        'max_per_day' => 'integer',
        'hours' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}

