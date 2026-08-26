<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'target', 'detail', 'ip_address', 'user_agent', 'device', 'browser', 'platform'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
