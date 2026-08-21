<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'document_name',
        'category',
        'related_entity_type',
        'related_entity_id',
        'expiry_date',
        'status',
        'file_path',
        'description',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];
}
