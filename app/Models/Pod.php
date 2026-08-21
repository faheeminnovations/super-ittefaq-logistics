<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pod extends Model
{
    protected $fillable = [
        'job_number',
        'customer_id',
        'driver_id',
        'delivery_datetime',
        'has_signature',
        'has_photo',
        'delivery_confirmation',
        'status',
        'signature_path',
        'photo_path',
        'notes',
        'job_id',
    ];

    protected $casts = [
        'delivery_datetime' => 'datetime',
        'has_signature' => 'boolean',
        'has_photo' => 'boolean',
        'delivery_confirmation' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class);
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class, 'job_id');
    }
}
