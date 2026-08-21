<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'job_number',
        'job_description',
        'vehicle_id',
        'driver_id',
        'pickup_location',
        'delivery_location',
        'status',
        'dispatch_time',
        'completion_time',
        'notes',
        'job_id',
    ];

    protected $casts = [
        'dispatch_time' => 'datetime',
        'completion_time' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class, 'job_id');
    }
}
