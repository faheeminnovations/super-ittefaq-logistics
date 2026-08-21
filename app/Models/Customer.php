<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'city',
        'credit_limit',
        'balance',
        'status',
        'phone',
        'address',
        'guarantor',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Get formatted credit limit with currency
     */
    public function getFormattedCreditLimitAttribute()
    {
        return CurrencyHelper::formatCurrency($this->credit_limit);
    }

    /**
     * Get formatted balance with currency
     */
    public function getFormattedBalanceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->balance);
    }
}
