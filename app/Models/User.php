<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id')->withTrashed();
    }

    public function isManager()
    {
        return in_array($this->role, ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng']);
    }
    public function isDownAdminRole()
    {
        return in_array($this->role, ['admin', 'phó cục trưởng', 'cục trưởng']);
    }
    public function gioiHanRole_1()
    {
        return in_array($this->role, ['admin', 'phó cục trưởng', 'cục trưởng', 'trợ lý cục trưởng']);
    }

    public function quanLyOnduty()
    {
        return in_array($this->position?->name_positions, ['Đội Trưởng', 'Đội Phó']);
    }
    public function canEditPositionOf(User $target)
    {
        return $this->id !== $target->id
            && $this->getRoleLevel() > $target->getRoleLevel();
    }

    public function getRoleLevel()
    {
        return match ($this->role) {
            'admin' => 5,
            'cục trưởng' => 4,
            'phó cục trưởng' => 3,
            'trợ lý cục trưởng' => 2,
            'thư ký' => 1,
            default => 0,
        };
    }
    public function getPositionLevel()
    {
        return match ($this->position->name_positions) {
            'Cục Trưởng' => 9,
            'Phó Cục Trưởng' => 8,
            'Trợ Lý Cục Trưởng' => 7,
            'Thư Ký' => 6,
            'Đội Trưởng' => 5,
            'Đội Phó' => 4,
            default => 0,
        };
    }

    public function effectiveSalaryRate(): float
    {
        // Nếu có lương cá nhân
        if ($this->salaryConfig) {
            return (float) $this->salaryConfig->hourly_rate;
        }

        // Nếu có lương theo chức vụ thông qua employee
        // if ($this->employee?->position?->salaryConfig) {
        //     return (float) $this->employee->position->salaryConfig->hourly_rate;
        // }

        if ($this->employee?->rank?->salaryConfig) {
            return (float) $this->employee->rank->salaryConfig->hourly_rate;
        }

        // Lương mặc định
        return 24000.0;
    }

    //// truy xuất hệ số của nhân viên
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function getPositionAttribute()
    {
        return $this->employee?->position;
    }

    public function salaryConfig()
    {
        return $this->hasOne(SalaryConfig::class);
    }

    //  Dùng ?-> (null-safe operator) để tránh lỗi nếu employee hoặc position là null.
    public function positionSalaryConfig()
    {
        return $this->employee?->position?->salaryConfig;
    }
    public function rankSalaryConfig()
    {
        return $this->employee?->rank?->salaryConfig;
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
    public function monthly_attendance_summaries()
    {
        return $this->hasMany(MonthlyAttendanceSummary::class, 'user_id', 'id');
    }
}
