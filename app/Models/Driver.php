<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'licence_no',
        'category',
        'cpc_expiry',
        'phone',
        'status',
        'address',
        'licence_expiry',
    ];

    protected $casts = [
        'cpc_expiry' => 'date',
        'licence_expiry' => 'date',
    ];
}
