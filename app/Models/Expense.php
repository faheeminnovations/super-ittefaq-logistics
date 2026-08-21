<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'category',
        'vehicle_id',
        'amount',
        'submitted_by',
        'status',
        'description',
        'receipt_url',
        'driver_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->amount);
    }
}
