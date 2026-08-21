<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'reg_no',
        'type',
        'make_model',
        'year',
        'mot_expiry',
        'insurance_expiry',
        'status',
        'fuel_capacity',
        'vin',
        'notes',
    ];

    protected $casts = [
        'mot_expiry' => 'date',
        'insurance_expiry' => 'date',
        'fuel_capacity' => 'decimal:2',
    ];

    public function getPlateNumberAttribute()
    {
        return $this->reg_no;
    }
}
