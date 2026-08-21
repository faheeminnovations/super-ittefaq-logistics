<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'latitude',
        'longitude',
        'location_description',
        'speed',
        'status',
        'job_number',
        'tracked_at',
        'job_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tracked_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class);
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }
}
