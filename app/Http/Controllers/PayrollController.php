<?php

namespace App\Http\Controllers;

use App\Models\{User, MonthlyAttendanceSummary, Employee, Attendance, ActivityLog};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $users = User::with(['employee.position', 'employee.rank'])
            ->whereHas('employee')
            ->get()
            ->sort(function ($a, $b) use ($positionOrder) {
                $aPos = $positionOrder[$a->employee?->position?->name_positions] ?? 999;
                $bPos = $positionOrder[$b->employee?->position?->name_positions] ?? 999;

                if ($aPos !== $bPos) {
                    return $aPos <=> $bPos; // so sánh theo position trước
                }

                $aRank = $a->employee?->rank?->id ?? 9999;
                $bRank = $b->employee?->rank?->id ?? 9999;

                return $bRank <=> $aRank; // rank id giảm dần
            })
            ->values();

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

        $tongTienLuongThang = array_reduce($summaries, function ($carry, $item) {
            return $carry + $item->total_wage;
        }, 0);
        $tongNhanVien = count($users);
        $tongNhanVienDaChamCong = count(array_filter($summaries, function ($summary) {
            return $summary->total_hours > 0;
        }));

        return view('payroll.index', compact('users', 'summaries', 'currentMonth', 'tongTienLuongThang', 'tongNhanVien', 'tongNhanVienDaChamCong'));
    }

    public function showUserAttendance(User $user)
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $attendances = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->get();

        $heSoLuong = $user->effectiveSalaryRate();
        $totalLuong = $user->monthly_attendance_summaries->flatten()->sum('total_wage');
        $now = now();
        $monthlyTotal = $attendances
            ->filter(function ($attendance) use ($now) {
                return Carbon::parse($attendance->date)->month === $now->month
                    && Carbon::parse($attendance->date)->year === $now->year;
            })
            ->sum('wage');
        // Hiển thị lịch sử tổng lương theo tháng hiện tại
        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
        // Hiển thị lịch sử bảng công theo tháng hiện tại
        $currentAttendances = $user->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();
        // Lấy tất cả bản ghi bảng lương trong tất cả tháng hiện có
        $attendancesAllMonthly = $user->attendances()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('payroll.attendance_history', compact('user', 'attendancesAllMonthly', 'currentAttendances', 'month', 'monthlySummaries', 'heSoLuong', 'totalLuong', 'monthlyTotal'));
    }

    // SEARCH, TÌM KIẾM NHÂN SỰ FETCH
    public function search(Request $request)
    {
        $query = $request->input('query');
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $employees = Employee::with([
            'user',
            'position.salaryConfig',
            'rank',
            'userCreatedBy',
            'user.attendances' => function ($q) {
                $q->whereMonth('date', now()->month) // Lọc theo tháng hiện tại
                    ->whereYear('date', now()->year); // Lọc theo năm hiện tại
            }
        ])
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
                        ],
                        'total_hours' => $emp->user->attendances->sum('duration'),
                        'total_wage' => $emp->user->attendances->sum('wage'),
                        'total_attendances' => $emp->user->attendances->count(),
                        'effective_salary_rate' => $emp->user->effectiveSalaryRate(),
                        'attendances' => $emp->user->attendances->map(function ($attendance) {
                            return [
                                'date' => $attendance->date,
                                'duration' => $attendance->duration,
                                'wage' => $attendance->wage,
                            ];
                        })->toArray(),
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
                // $q->whereNotIn('role', ['admin', 'Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng', 'Thư Ký']); // 👈 Bỏ tài khoản admin
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
    protected function updateMonthlySummary(int $userId, int $month, int $year)
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth()->endOfDay();

        // Nếu cần chỉ tính bản ghi "Hoàn thành", bật filter dưới đây:
        // ->where('status', 'Hoàn thành')
        $rows = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get();

        $totalHours = round($rows->sum('duration'), 2);
        $totalWage = (int) $rows->sum('wage');

        return MonthlyAttendanceSummary::updateOrCreate(
            ['user_id' => $userId, 'month' => $month, 'year' => $year],
            ['total_hours' => $totalHours, 'total_wage' => $totalWage]
        );
    }

    public function deleteAttendance($id)
    {
        $user = Auth::user();
        $attendance = Attendance::findOrFail($id);

        // Kiểm tra quyền hạn nếu cần (admin, supervisor,...)
        if (!auth()->user()->isDownAdminRole()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $attendance->delete();

        // Cập nhật lại tổng kết tháng
        $this->updateMonthlySummary($attendance->user_id, Carbon::parse($attendance->date)->month, Carbon::parse($attendance->date)->year);

        // Ghi log hành động
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logsCustom',
            'target' => $attendance->user->username,
            'detail' => "XÓA BẢN GHI CHẤM CÔNG ID: {$attendance->id} NGÀY: {$attendance->date}",
        ]);

        return back()->with('success', 'Đã xóa bản ghi chấm công.');
    }
    ////
    public function updateInline(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'wage') {
            // wage chỉ nhận số nguyên
            $value = (int) preg_replace('/[^0-9]/', '', $value);
        }

        if ($field === 'duration') {
            // duration nhận số thực (float), cho phép dấu chấm
            $value = (float) preg_replace('/[^0-9\.]/', '', $value);
        }

        $summary = DB::transaction(function () use ($attendance, $field, $value) {
            $attendance->{$field} = $value;
            $attendance->save();

            $month = Carbon::parse($attendance->date)->month;
            $year = Carbon::parse($attendance->date)->year;

            return $this->updateMonthlySummary($attendance->user_id, $month, $year);
        });

        // Ghi log hành động
        $user = Auth::user();
        $nameUser = $attendance->user->employee->name_ingame ?? $attendance->user->username;
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logsCustom',
            'target' => $attendance->user->username,
            'detail' => "Thay đổi dữ liệu chấm công của {$nameUser} Tại ID: {$attendance->id}",
        ]);

        return response()->json([
            'success' => true,
            'summary' => [
                'month' => $summary->month,
                'year' => $summary->year,
                'total_hours' => $summary->total_hours,
                'total_wage' => $summary->total_wage,
                'total_hours_formatted' => number_format($summary->total_hours, 2),
                'total_wage_formatted' => number_format($summary->total_wage),
            ],
        ]);
    }

}
