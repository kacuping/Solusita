<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'dob',
        'avatar',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Relasi ke User (akun login yang terkait dengan pelanggan).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booking yang dimiliki pelanggan.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
