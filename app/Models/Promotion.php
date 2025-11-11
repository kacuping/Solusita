<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'active',
        'usage_limit',
        'used_count',
        'segment_rules',
    ];

    protected $casts = [
        'active' => 'boolean',
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'segment_rules' => 'array',
    ];
}
