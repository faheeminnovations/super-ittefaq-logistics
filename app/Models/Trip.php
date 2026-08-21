<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Trip extends Model
{
    protected $fillable = [
        'trip_number',
        'job_number',
        'vehicle_id',
        'driver_id',
        'pickup_time',
        'delivery_time',
        'status',
        'pickup_location',
        'delivery_location',
        'distance',
        'notes',
        'job_id',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
        'distance' => 'decimal:2',
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

    /**
     * Get formatted distance with unit
     */
    public function getFormattedDistanceAttribute()
    {
        $unit = CurrencyHelper::getDistanceUnit();
        return number_format($this->distance, 2) . ' ' . $unit;
    }
}
