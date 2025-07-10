<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionSalaryConfig extends Model
{
    protected $fillable = ['position_id', 'hourly_rate', 'max_hours_per_day', 'updated_by'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

