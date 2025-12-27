<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{

    use SoftDeletes;

    // Sắp xếp nhân sự
    public function scopeSortedByCustomPosition(Builder $query)
    {
        $positionOrder = [
            'Cục Trưởng' => 1,
            'Phó Cục Trưởng' => 2,
            'Trợ Lý Cục Trưởng' => 3,
            'Thư Ký' => 4,
            'Đội Trưởng' => 5,
            'Đội Phó' => 6,
            'Cảnh Sát Viên' => 7,
            'Sĩ Quan Dự Bị' => 8,
            'Thực Tập' => 9,
        ];
        // Ghi chú: Vì sort() là phương thức của collection (không phải query builder), ta phải gọi .get() trước rồi mới sắp xếp.
        return $query->with(['user', 'position', 'rank', 'userCreatedBy'])
            ->get()
            ->sort(function ($a, $b) use ($positionOrder) {
                $aPos = $positionOrder[$a->position->name_positions] ?? 999;
                $bPos = $positionOrder[$b->position->name_positions] ?? 999;

                if ($aPos === $bPos) {
                    // Sắp xếp theo rank_id từ cao xuống thấp
                    return $b->rank_id <=> $a->rank_id;
                }

                return $aPos <=> $bPos;
            });
    }
    public function scopeSortedActiveOnly($query)
    {
        return $query->whereNull('deleted_at')->with(['user', 'position', 'rank', 'userCreatedBy']);
    }

    protected $fillable = [
        'user_id',
        'name_ingame',
        'position_id',
        'rank_id',
        'avatar',
        'created_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function userCreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by'); // nếu bạn lưu người tạo
    }
}
