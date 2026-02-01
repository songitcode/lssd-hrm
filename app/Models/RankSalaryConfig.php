<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankSalaryConfig extends Model
{
    protected $fillable = ['rank_id', 'hourly_rate', 'max_hours_per_day', 'updated_by'];

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
