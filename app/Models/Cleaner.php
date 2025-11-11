<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Cleaner extends Model
{
    protected $fillable = [
        'name',
        'full_name',
        'address',
        'phone',
        'birth_place',
        'birth_date',
        'bank_account_number',
        'bank_name',
        'bank_account_name',
        'status',
        'active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
    ];

    protected $appends = ['age'];

    /**
     * Hitung umur dari birth_date.
     */
    public function age(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->birth_date) {
                return null;
            }
            return Carbon::parse($this->birth_date)->age;
        });
    }
}
