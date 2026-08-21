<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_name',
        'report_type',
        'report_date',
        'revenue',
        'expenses',
        'profit',
        'total_mileage',
        'total_jobs',
        'completed_jobs',
        'generated_data',
    ];

    protected $casts = [
        'report_date' => 'date',
        'revenue' => 'decimal:2',
        'expenses' => 'decimal:2',
        'profit' => 'decimal:2',
    ];
}
