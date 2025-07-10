<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    // Chức vụ quan hệ với Salary để có thể chỉnh sửa hệ số lương
    public function salaryConfig()
    {
        return $this->hasOne(PositionSalaryConfig::class);
    }

    // Mối quan hệ một chức vụ thuộc nhiều quân hàm
    public function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }
}
