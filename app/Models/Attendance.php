<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'duration',
        'wage',
        'status',
        'date',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDurationAttribute()
    {
        return number_format($this->duration, 2) . 'h';
    }

    public function getFormattedWageAttribute()
    {
        return number_format($this->wage, 0) . ' $';
    }
}
