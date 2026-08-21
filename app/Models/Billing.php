<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;
use Carbon\Carbon;

class Billing extends Model
{
    protected $fillable = [
        'sr',
        'date',
        'vehicle_no',
        'customer_name',
        'contact_number',
        'bags',
        'delivery_point',
        'km_covered',
        'rent',
        'advance',
        'advance_date',
        'guarantor',
        'dues',
        'status',
        'billing_month',
    ];

    protected $casts = [
        'date' => 'date',
        'advance_date' => 'date',
        'km_covered' => 'decimal:2',
        'rent' => 'decimal:2',
        'advance' => 'decimal:2',
        'dues' => 'decimal:2',
        'bags' => 'integer',
    ];

    /**
     * Get formatted serial number
     */
    public function getFormattedSrAttribute()
    {
        return $this->sr ?? '-';
    }

    /**
     * Get formatted date in DD-Mon-YY format
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d-M-y') : '-';
    }

    /**
     * Get formatted vehicle number
     */
    public function getFormattedVehicleNoAttribute()
    {
        return strtoupper($this->vehicle_no ?? '-');
    }

    /**
     * Get formatted customer name
     */
    public function getFormattedCustomerNameAttribute()
    {
        return ucwords(strtolower($this->customer_name ?? '-'));
    }

    /**
     * Get formatted contact number
     */
    public function getFormattedContactNumberAttribute()
    {
        return $this->contact_number ?? '-';
    }

    /**
     * Get formatted bags count
     */
    public function getFormattedBagsAttribute()
    {
        return $this->bags ?? 0;
    }

    /**
     * Get formatted delivery point
     */
    public function getFormattedDeliveryPointAttribute()
    {
        return ucwords(strtolower($this->delivery_point ?? '-'));
    }

    /**
     * Get formatted distance with unit
     */
    public function getFormattedKmCoveredAttribute()
    {
        $unit = CurrencyHelper::getDistanceUnit();
        return number_format($this->km_covered ?? 0, 2) . ' ' . $unit;
    }

    /**
     * Get formatted rent amount with currency
     */
    public function getFormattedRentAmountAttribute()
    {
        return CurrencyHelper::formatAsPKRLower($this->rent ?? 0);
    }

    /**
     * Get formatted advance amount with currency
     */
    public function getFormattedAdvanceAmountAttribute()
    {
        return CurrencyHelper::formatAsPKRLower($this->advance ?? 0);
    }

    /**
     * Get formatted advance date
     */
    public function getFormattedAdvanceDateAttribute()
    {
        return $this->advance_date ? $this->advance_date->format('d-M-y') : '-';
    }

    /**
     * Get formatted guarantor name
     */
    public function getFormattedGuarantorAttribute()
    {
        return $this->guarantor ? ucwords(strtolower($this->guarantor)) : '-';
    }

    /**
     * Get formatted dues amount with currency
     */
    public function getFormattedDuesAmountAttribute()
    {
        return CurrencyHelper::formatAsPKRLower($this->dues ?? 0);
    }

    /**
     * Get formatted status with badge class
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status ?? 'Pending');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'Paid':
                return 'success';
            case 'Pending':
                return 'danger';
            case 'Partial':
                return 'warning';
            default:
                return 'secondary';
        }
    }

    /**
     * Scope to filter by billing month
     */
    public function scopeByMonth($query, $month)
    {
        return $query->where('billing_month', $month);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', ucfirst($status));
    }

    /**
     * Scope to filter by vehicle
     */
    public function scopeByVehicle($query, $vehicleNo)
    {
        return $query->where('vehicle_no', $vehicleNo);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer($query, $customerName)
    {
        return $query->where('customer_name', 'like', '%' . $customerName . '%');
    }
}
