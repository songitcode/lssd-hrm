<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyAttendanceSummary extends Model
{
    protected $table = 'monthly_attendance_summaries';

    protected $fillable = [
        'user_id',
        'period_type',
        'month',
        'year',
        'period_start',
        'period_end',
        'total_hours',
        'total_wage',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'total_wage' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}