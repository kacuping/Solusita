<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'base_price', 'duration_minutes', 'category', 'slug', 'active', 'icon'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
