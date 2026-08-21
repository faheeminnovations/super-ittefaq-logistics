<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'operator_licence_no',
        'vat_number',
        'ntn_number',
        'strn_number',
        'currency',
        'distance_unit',
        'document_reminder_window',
        'address',
        'phone',
        'email',
        'website',
        'gst_rate',
        'invoice_prefix',
        'quotation_prefix',
        'bank_name',
        'bank_account_number',
        'bank_iban',
    ];
}
