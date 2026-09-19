<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyOnlineStat extends Model
{
    protected $fillable = ['date', 'peak_online'];

    protected function casts(): array
    {
        return ['date' => 'date', 'peak_online' => 'integer'];
    }
}