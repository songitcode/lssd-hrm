<?php

namespace App\Http\Controllers;

use App\Models\{User, MonthlyAttendanceSummary, Employee, Attendance, ActivityLog, WorkHourConfig};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    // ─── Thứ tự chức vụ (dùng chung) ────────────────────────────────────────
    private const POSITION_ORDER = [
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

    // ─── Helper lấy cài đặt & kỳ hiện tại ───────────────────────────────────

    private function config(): WorkHourConfig
    {
        return WorkHourConfig::latestConfig();
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function index()
    {
        $config = $this->config();
        $period = $config->getCurrentPeriod();
        $currentMonth = $period['month'];

        $users = User::with(['employee.position', 'employee.rank'])
            ->whereHas('employee')
            ->get()
            ->sort(function ($a, $b) {
                $aPos = self::POSITION_ORDER[$a->employee?->position?->name_positions] ?? 999;
                $bPos = self::POSITION_ORDER[$b->employee?->position?->name_positions] ?? 999;
                if ($aPos !== $bPos)
                    return $aPos <=> $bPos;
                return ($b->employee?->rank?->id ?? 9999) <=> ($a->employee?->rank?->id ?? 9999);
            })
            ->values();

        $summaries = [];
        foreach ($users as $user) {
            $attendances = $user->attendances()
                ->whereBetween('date', [$period['start']->toDateString(), $period['end']->toDateString()])
                ->get();

            $summaries[$user->id] = (object) [
                'total_minutes' => round($attendances->sum('duration') * 60),
                'total_hours' => round($attendances->sum('duration'), 2),
                'total_wage' => $attendances->sum('wage'),
            ];
        }

        $tongTienLuongThang = array_sum(array_column((array) $summaries, 'total_wage'));
        $tongNhanVien = count($users);
        $tongNhanVienDaChamCong = count(array_filter($summaries, fn($s) => $s->total_hours > 0));

        return view('payroll.index', compact(
            'users',
            'summaries',
            'currentMonth',
            'tongTienLuongThang',
            'tongNhanVien',
            'tongNhanVienDaChamCong',
            'config',
            'period'
        ));
    }

    // ─── CÀI ĐẶT CHU KỲ ─────────────────────────────────────────────────────

    /**
     * GET /payroll/cycle-setting
     * Trả về cài đặt hiện tại dạng JSON (dùng cho modal).
     */
    public function getCycleSetting()
    {
        $c = $this->config();
        return response()->json([
            'cycle_type' => $c->cycle_type,
            'biweekly_reference_date' => $c->biweekly_reference_date?->toDateString(),
        ]);
    }

    /**
     * POST /payroll/cycle-setting
     * Manager lưu chu kỳ mới vào work_hour_configs.
     */
    public function updateCycleSetting(Request $request)
    {
        $data = $request->validate([
            'cycle_type' => ['required', 'in:monthly,biweekly'],
            'biweekly_reference_date' => [
                'nullable',
                'required_if:cycle_type,biweekly',
                'date',
                function ($attr, $value, $fail) {
                    // Phải là thứ Hai
                    if ($value && Carbon::parse($value)->dayOfWeek !== Carbon::MONDAY) {
                        $fail('Ngày mốc phải là thứ Hai (Monday).');
                    }
                },
            ],
        ]);

        $config = $this->config();
        $config->cycle_type = $data['cycle_type'];
        $config->biweekly_reference_date = $data['biweekly_reference_date'] ?? null;
        $config->updated_by = Auth::id();
        $config->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logsCustom',
            'target' => 'PayrollCycle',
            'detail' => 'Đổi chu kỳ lương → ' . $data['cycle_type']
                . ($data['biweekly_reference_date'] ? ' | Mốc: ' . $data['biweekly_reference_date'] : ''),
        ]);

        $period = $config->getCurrentPeriod();

        return response()->json([
            'success' => true,
            'message' => $data['cycle_type'] === 'monthly'
                ? 'Đã chuyển sang tính lương theo tháng.'
                : 'Đã chuyển sang tính lương theo 2 tuần.',
            'period' => $period['label'],
        ]);
    }

    // ─── CHI TIẾT CHẤM CÔNG TỪNG NGƯỜI ──────────────────────────────────────

    public function showUserAttendance(User $user)
    {
        $config = $this->config();
        $period = $config->getCurrentPeriod();
        $month = $period['month'];

        $heSoLuong = $user->effectiveSalaryRate();
        $totalLuong = $user->monthly_attendance_summaries->flatten()->sum('total_wage');

        $monthlyTotal = $user->attendances()
            ->whereBetween('date', [$period['start']->toDateString(), $period['end']->toDateString()])
            ->sum('wage');

        $currentAttendances = $user->attendances()
            ->whereBetween('date', [$period['start']->toDateString(), $period['end']->toDateString()])
            ->orderBy('date')
            ->get();

        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('period_start')
            ->get();

        $attendancesAllMonthly = $user->attendances()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('payroll.attendance_history', compact(
            'user',
            'attendancesAllMonthly',
            'currentAttendances',
            'month',
            'monthlySummaries',
            'heSoLuong',
            'totalLuong',
            'monthlyTotal',
            'config',
            'period'
        ));
    }

    // ─── SEARCH ──────────────────────────────────────────────────────────────

    public function search(Request $request)
    {
        $query = $request->input('query');
        $config = $this->config();
        $period = $config->getCurrentPeriod();

        $employees = Employee::with([
            'user',
            'position.salaryConfig',
            'rank',
            'userCreatedBy',
            'user.attendances' => function ($q) use ($period) {
                $q->whereBetween('date', [
                    $period['start']->toDateString(),
                    $period['end']->toDateString(),
                ]);
            }
        ])
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'admin'))
            ->where(function ($q) use ($query) {
                $q->where('name_ingame', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('username', 'LIKE', "%{$query}%"));
            })
            ->get();

        // Lấy summary kỳ hiện tại
        $summaryQ = MonthlyAttendanceSummary::where('period_type', $config->cycle_type)
            ->where('month', $period['month'])
            ->where('year', $period['year']);

        if ($config->cycle_type === 'biweekly') {
            $summaryQ->where('period_start', $period['period_start']);
        }

        $summaries = $summaryQ->get()->keyBy('user_id');

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
                            'position' => ['salary_config' => ['hourly_rate' => $rate]]
                        ],
                        'total_hours' => $emp->user->attendances->sum('duration'),
                        'total_wage' => $emp->user->attendances->sum('wage'),
                        'total_attendances' => $emp->user->attendances->count(),
                        'effective_salary_rate' => $emp->user->effectiveSalaryRate(),
                        'attendances' => $emp->user->attendances->map(fn($a) => [
                            'date' => $a->date,
                            'duration' => $a->duration,
                            'wage' => $a->wage,
                        ])->toArray(),
                    ],
                    'position' => ['name_positions' => $emp->position->name_positions ?? null],
                    'rank' => ['name_ranks' => $emp->rank->name_ranks ?? null],
                    'avatar' => $emp->avatar,
                    'created_at' => $emp->created_at,
                    'user_created_by' => ['username' => $emp->userCreatedBy->username ?? null],
                    'summary' => $summary ? [
                        'total_minutes' => round($summary->total_hours * 60),
                        'total_hours' => $summary->total_hours,
                        'total_wage' => $summary->total_wage,
                    ] : null,
                    'attendance_url' => route('payroll.user_attendance', $emp->user),
                ];
            }),
        ]);
    }

    // ─── BẢNG LƯƠNG KỲ TRƯỚC ────────────────────────────────────────────────

    public function previousMonthPayroll()
    {
        $config = $this->config();
        $prev = $config->getPreviousPeriod();

        // Tải tất cả users không phải admin, kèm quan hệ cần thiết
        $users = User::with([
            'employee.position.salaryConfig',
            'employee.rank',
        ])
            ->where('role', '!=', 'admin')
            ->whereHas('employee')
            ->get()
            ->sort(
                fn($a, $b) =>
                (self::POSITION_ORDER[$a->employee?->position?->name_positions] ?? 999)
                <=> (self::POSITION_ORDER[$b->employee?->position?->name_positions] ?? 999)
            )
            ->values();

        $results = [];

        foreach ($users as $user) {
            // Tính từ bảng Attendance (không phụ thuộc summary đã lưu hay chưa)
            $rows = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [
                    $prev['start']->toDateString(),
                    $prev['end']->toDateString(),
                ])->get();

            $totalHours = round($rows->sum('duration'), 2);
            $totalWage = (int) $rows->sum('wage');

            // Auto-save / cập nhật summary để lần sau nhanh hơn (không bắt buộc)
            MonthlyAttendanceSummary::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'period_type' => $config->cycle_type,
                    'month' => $prev['month'],
                    'year' => $prev['year'],
                    'period_start' => $prev['period_start'],
                ],
                [
                    'total_hours' => $totalHours,
                    'total_wage' => $totalWage,
                    'period_end' => $prev['period_end'],
                ]
            );

            // Chỉ trả về user có dữ liệu trong kỳ này
            if ($totalHours <= 0 && $totalWage <= 0) {
                continue;
            }

            $results[] = [
                'user' => $user,
                'total_hours' => $totalHours,
                'total_wage' => $totalWage,
            ];
        }

        return response()->json([
            'data' => $results,
            'period' => $prev['label'],
        ]);
    }

    public function deletePreviousMonth()
    {
        $config = $this->config();
        $prev = $config->getPreviousPeriod();

        $q = MonthlyAttendanceSummary::where('period_type', $config->cycle_type)
            ->where('month', $prev['month'])
            ->where('year', $prev['year']);

        if ($config->cycle_type === 'biweekly') {
            $q->where('period_start', $prev['period_start']);
        }

        $q->delete();

        return response()->json(['message' => 'Đã xóa bảng lương kỳ trước (' . $prev['label'] . ')']);
    }

    // ─── TỔNG KẾT KỲ (nội bộ) ───────────────────────────────────────────────

    /**
     * Cập nhật / tạo mới bản tóm tắt lương cho user trong kỳ chứa $attendanceDate.
     * Dùng ngày chấm công thực tế thay vì month/year để xác định kỳ biweekly chính xác.
     */
    protected function updateMonthlySummary(int $userId, string $attendanceDate): MonthlyAttendanceSummary
    {
        $config = $this->config();
        $day = Carbon::parse($attendanceDate);

        // Xác định period bằng ngày thực tế của bản ghi chấm công
        if ($config->cycle_type === 'biweekly') {
            $period = $config->buildBiweeklyFromDay($day);
        } else {
            $start = $day->copy()->startOfMonth()->startOfDay();
            $end = $day->copy()->endOfMonth()->endOfDay();
            $period = [
                'type' => 'monthly',
                'start' => $start,
                'end' => $end,
                'month' => $day->month,
                'year' => $day->year,
                'period_start' => null,
                'period_end' => null,
            ];
        }

        $rows = Attendance::where('user_id', $userId)
            ->whereBetween('date', [
                $period['start']->toDateString(),
                $period['end']->toDateString(),
            ])->get();

        return MonthlyAttendanceSummary::updateOrCreate(
            [
                'user_id' => $userId,
                'period_type' => $period['type'],
                'month' => $period['month'],
                'year' => $period['year'],
                'period_start' => $period['period_start'],
            ],
            [
                'total_hours' => round($rows->sum('duration'), 2),
                'total_wage' => (int) $rows->sum('wage'),
                'period_end' => $period['period_end'],
            ]
        );
    }

    // ─── XÓA / SỬA CHẤM CÔNG ────────────────────────────────────────────────

    public function deleteAttendance($id)
    {
        $user = Auth::user();
        $attendance = Attendance::findOrFail($id);

        if (!auth()->user()->isDownAdminRole()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $attendance->delete();

        // Truyền ngày thực tế để xác định đúng kỳ (monthly hoặc biweekly)
        $this->updateMonthlySummary($attendance->user_id, (string) $attendance->date);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logsCustom',
            'target' => $attendance->user->username,
            'detail' => "XÓA BẢN GHI CHẤM CÔNG ID: {$attendance->id} NGÀY: {$attendance->date}",
        ]);

        return back()->with('success', 'Đã xóa bản ghi chấm công.');
    }

    public function updateInline(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'wage')
            $value = (int) preg_replace('/[^0-9]/', '', $value);
        if ($field === 'duration')
            $value = (float) preg_replace('/[^0-9\.]/', '', $value);

        $summary = DB::transaction(function () use ($attendance, $field, $value) {
            $attendance->{$field} = $value;
            $attendance->save();

            // Dùng ngày thực tế để tính đúng kỳ biweekly / monthly
            return $this->updateMonthlySummary($attendance->user_id, (string) $attendance->date);
        });

        $nameUser = $attendance->user->employee->name_ingame ?? $attendance->user->username;
        ActivityLog::create([
            'user_id' => Auth::id(),
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