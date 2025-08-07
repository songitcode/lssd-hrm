<?php

namespace App\Http\Controllers;

use App\Models\{User, MonthlyAttendanceSummary, Employee, Attendance};
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

    // SEARCH, TÌM KIẾM NHÂN SỰ FETCH
    public function search(Request $request)
    {
        $query = $request->input('query');
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $employees = Employee::with(['user', 'position.salaryConfig', 'rank', 'userCreatedBy'])
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin');
            })
            ->where(function ($q) use ($query) {
                $q->where('name_ingame', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('username', 'LIKE', "%{$query}%"));
            })
            ->get();

        // Lấy tất cả bản ghi bảng lương trong tháng hiện tại
        $summaries = MonthlyAttendanceSummary::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get()
            ->keyBy('user_id'); // Để truy cập nhanh theo user_id

        return response()->json([
            'data' => $employees->map(function ($emp) use ($summaries) {
                $userId = $emp->user->id ?? null;
                $summary = $userId && $summaries->has($userId) ? $summaries[$userId] : null;
                $rate = $emp->position->salaryConfig->hourly_rate ?? 24000;

                return [
                    'id' => $emp->id,
                    'name_ingame' => $emp->name_ingame,
                    'user' => [
                        'username' => $emp->user->username ?? null,
                        'employee' => [
                            'position' => [
                                'salary_config' => [
                                    'hourly_rate' => $rate
                                ]
                            ]
                        ]
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
                    'summary' => $summary ? [
                        'total_minutes' => $summary->total_minutes,
                        'total_hours' => $summary->total_hours,
                        'total_wage' => $summary->total_wage,
                    ] : null,
                    'attendance_url' => route('payroll.user_attendance', $emp->user),
                ];
            }),
        ]);
    }
    ////

    // XEM BẢNG LƯƠNG THÁNG TRƯỚC
    public function previousMonthPayroll()
    {
        $date = now()->subMonth();

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

        $payrolls = MonthlyAttendanceSummary::with('user.employee.position', 'user.employee.rank', 'user.employee.position.salaryConfig')
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin'); // 👈 Bỏ tài khoản admin
            })
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->get()
            ->sortBy(function ($summary) use ($positionOrder) {
                return $positionOrder[$summary->user->employee?->position?->name_positions] ?? 999;
            })->values();

        return response()->json(['data' => $payrolls]);
    }

    // Xóa bảng lương tháng trước
    public function deletePreviousMonth()
    {
        $month = now()->subMonth()->month;
        $year = now()->subMonth()->year;

        MonthlyAttendanceSummary::where('month', $month)
            ->where('year', $year)
            ->delete();

        return response()->json(['message' => 'Đã xóa bảng lương tháng trước']);
    }
    ////
}
