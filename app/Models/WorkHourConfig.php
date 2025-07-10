<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkHourConfig extends Model
{
    protected $fillable = ['max_hours_per_day', 'updated_by'];

    // Hàm trả về bản ghi mới nhất (cấu hình giờ làm hiện tại)
    public static function latestConfig()
    {
        return self::latest()->first();
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function currentMaxHour()
    {
        return static::latest()->first()?->max_hours_per_day ?? 3.0;
    }
}
