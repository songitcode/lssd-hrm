<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorSession extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'ip_address', 'user_agent',
        'device_type', 'browser', 'last_activity',
    ];

    protected function casts(): array
    {
        return ['last_activity' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}