<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyAttendanceSummary extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'period_type',   // 'monthly' | 'biweekly'
        'period_start',  // date string, NULL khi monthly
        'period_end',    // date string, NULL khi monthly
        'total_hours',
        'total_wage',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
