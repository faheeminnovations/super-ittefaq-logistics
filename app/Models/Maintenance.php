<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Maintenance extends Model
{
    protected $fillable = [
        'vehicle_id',
        'service_type',
        'service_date',
        'workshop',
        'cost',
        'status',
        'description',
        'mileage',
    ];

    protected $casts = [
        'service_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get formatted cost with currency
     */
    public function getFormattedCostAttribute()
    {
        return CurrencyHelper::formatCurrency($this->cost);
    }

    /**
     * Get formatted mileage with distance unit
     */
    public function getFormattedMileageAttribute()
    {
        $unit = CurrencyHelper::getDistanceUnit();
        return number_format($this->mileage, 2) . ' ' . $unit;
    }
}
