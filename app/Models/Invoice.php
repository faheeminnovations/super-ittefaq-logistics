<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'amount',
        'vat',
        'due_date',
        'status',
        'invoice_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat' => 'decimal:2',
        'due_date' => 'date',
        'invoice_date' => 'date',
        'paid_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->amount);
    }

    /**
     * Get formatted VAT with currency
     */
    public function getFormattedVatAttribute()
    {
        return CurrencyHelper::formatCurrency($this->vat);
    }

    /**
     * Get total amount including VAT
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->vat;
    }

    /**
     * Get formatted total amount with currency
     */
    public function getFormattedTotalAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_amount);
    }
}
