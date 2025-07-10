<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
