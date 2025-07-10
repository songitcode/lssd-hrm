<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyAttendanceSummary extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_hours',
        'total_wage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
