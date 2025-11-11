<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id', 'service_id', 'cleaner_id', 'scheduled_at', 'status', 'address', 'notes', 'duration_minutes', 'total_amount', 'payment_status', 'promotion_code'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
