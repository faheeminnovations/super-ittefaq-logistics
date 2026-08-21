<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'module',
        'email_enabled',
        'app_enabled',
        'whatsapp_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'app_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
