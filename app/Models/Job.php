<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Job extends Model
{
    protected $table = 'transport_jobs';

    protected $fillable = [
        'job_number',
        'customer_id',
        'pickup_location',
        'delivery_location',
        'job_date',
        'status',
        'vehicle_id',
        'driver_id',
        'notes',
        'quoted_price',
        'bags',
        'rent',
        'advance',
        'advance_date',
        'dues',
    ];

    protected $casts = [
        'job_date' => 'date',
        'quoted_price' => 'decimal:2',
        'rent' => 'decimal:2',
        'advance' => 'decimal:2',
        'dues' => 'decimal:2',
        'advance_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trips()
    {
        return $this->hasMany(\App\Models\Trip::class, 'job_id');
    }

    /**
     * Get formatted quoted price with currency
     */
    public function getFormattedQuotedPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->quoted_price);
    }

    /**
     * Get formatted rent amount with currency
     */
    public function getFormattedRentAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->rent);
    }

    /**
     * Get formatted advance amount with currency
     */
    public function getFormattedAdvanceAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->advance);
    }

    /**
     * Get formatted dues amount with currency
     */
    public function getFormattedDuesAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->dues);
    }
}
