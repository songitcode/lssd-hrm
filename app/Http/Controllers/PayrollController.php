<?php

namespace App\Http\Controllers;

use App\Models\{User, MonthlyAttendanceSummary, Employee};
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

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


        // Lấy tất cả user có employee và position
        $users = User::with(['employee.position', 'rank'])
            ->whereHas('employee')
            ->get()
            ->sortBy(function ($user) use ($positionOrder) {
                return $positionOrder[$user->employee?->position?->name_positions] ?? 999;
            })->values(); // reset key sau khi sort

        $summaries = [];

        foreach ($users as $user) {
            $attendances = $user->attendances()
                ->whereMonth('date', now()->month)
                ->get();

            $totalMinutes = $attendances->sum('duration') * 60; // duration tính bằng giờ
            $totalHours = $attendances->sum('duration');
            $totalWage = $attendances->sum('wage');
            $rate = $user->effectiveSalaryRate();

            $summaries[$user->id] = (object) [
                'total_minutes' => round($totalMinutes),
                'total_hours' => round($totalHours, 2),
                'total_wage' => $totalWage,
            ];
        }

        return view('payroll.index', compact('users', 'summaries', 'currentMonth'));
    }

    public function showUserAttendance(User $user)
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Hiển thị lịch sử tổng lương theo tháng
        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();


        $attendances = $user->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        return view('payroll.attendance_history', compact('user', 'attendances', 'month', 'monthlySummaries'));
    }

    //// SEARCH, TÌM KIẾM NHÂN SỰ FETCH
    public function search(Request $request)
    {
        $query = $request->input('query');

        $employees = Employee::with(['user', 'position', 'rank', 'userCreatedBy'])
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin'); // 👈 Bỏ tài khoản admin
            })
            ->where(function ($q) use ($query) {
                $q->where('name_ingame', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('username', 'LIKE', "%{$query}%"));
            })
            ->get();

        return response()->json([
            'data' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name_ingame' => $emp->name_ingame,
                    'user' => [
                        'username' => $emp->user->username ?? null,
                    ],
                    'position' => [
                        'name_positions' => $emp->position->name_positions ?? null,
                    ],
                    'rank' => [
                        'name_ranks' => $emp->rank->name_ranks ?? null,
                    ],
                    'avatar' => $emp->avatar,
                    'created_at' => $emp->created_at,
                    'user_created_by' => [
                        'username' => $emp->userCreatedBy->username ?? null,
                    ],
                    'attendance_url' => route('payroll.user_attendance', $emp->user),
                ];
            }),
        ]);
    }

}
