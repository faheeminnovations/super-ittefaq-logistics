<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'module',
        'data',
        'is_read',
        'sent_email',
        'sent_whatsapp',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'sent_email' => 'boolean',
        'sent_whatsapp' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
