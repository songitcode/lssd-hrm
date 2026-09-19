<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'ip_address', 'url', 'route_name',
        'user_agent', 'device_type', 'browser',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}