<?php

namespace App\Http\Controllers;

use App\Models\{User, MonthlyAttendanceSummary, Employee, Attendance, ActivityLog, PayrollSetting};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    // ─── Thứ tự chức vụ ──────────────────────────────────────────────────────
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

    // ─── Helper: lấy cài đặt & kỳ hiện tại ──────────────────────────────────

    private function activeSetting(): PayrollSetting
    {
        return PayrollSetting::current();
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function index()
    {
        $setting = $this->activeSetting();
        $period = $setting->getCurrentPeriod();

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
                ->whereBetween('date', [$period['start'], $period['end']])
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

        // Giữ biến cũ để view không cần sửa nhiều
        $currentMonth = $period['month'];

        return view('payroll.index', compact(
            'users',
            'summaries',
            'currentMonth',
            'tongTienLuongThang',
            'tongNhanVien',
            'tongNhanVienDaChamCong',
            'setting',
            'period'
        ));
    }

    // ─── SETTING: XEM / ĐỔI CHU KỲ ─────────────────────────────────────────

    /**
     * GET /payroll/setting
     * Trả về setting hiện tại dạng JSON (dùng cho modal AJAX).
     */
    public function getSetting()
    {
        $setting = $this->activeSetting();
        return response()->json([
            'cycle_type' => $setting->cycle_type,
            'biweekly_reference_date' => $setting->biweekly_reference_date?->toDateString(),
        ]);
    }

    /**
     * POST /payroll/setting
     * Manager đổi chu kỳ tính lương.
     *
     * Body: { cycle_type: 'monthly'|'biweekly', biweekly_reference_date?: 'YYYY-MM-DD' }
     */
    public function updateSetting(Request $request)
    {
        $data = $request->validate([
            'cycle_type' => ['required', Rule::in(['monthly', 'biweekly'])],
            'biweekly_reference_date' => [
                'nullable',
                'required_if:cycle_type,biweekly',
                'date',
                // Phải là thứ Hai
                function ($attr, $value, $fail) {
                    if ($value && Carbon::parse($value)->dayOfWeek !== Carbon::MONDAY) {
                        $fail('Ngày mốc phải là thứ Hai (Monday).');
                    }
                },
            ],
        ]);

        $setting = $this->activeSetting();
        $setting->update($data);

        // Ghi log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logsCustom',
            'target' => 'PayrollSetting',
            'detail' => 'Đổi chu kỳ lương sang: ' . $data['cycle_type']
                . ($data['biweekly_reference_date'] ? ' | Mốc: ' . $data['biweekly_reference_date'] : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => $data['cycle_type'] === 'monthly'
                ? 'Đã chuyển sang tính lương theo tháng.'
                : 'Đã chuyển sang tính lương theo 2 tuần.',
            'setting' => $setting->fresh(),
        ]);
    }

    // ─── CHI TIẾT CHẤM CÔNG TỪNG NGƯỜI ──────────────────────────────────────

    public function showUserAttendance(User $user)
    {
        $setting = $this->activeSetting();
        $period = $setting->getCurrentPeriod();

        $month = $period['month'];
        $year = $period['year'];

        $heSoLuong = $user->effectiveSalaryRate();

        // Tổng lương kỳ hiện tại (theo date range)
        $monthlyTotal = $user->attendances()
            ->whereBetween('date', [$period['start'], $period['end']])
            ->sum('wage');

        // Tổng lương tất cả thời gian
        $totalLuong = $user->monthly_attendance_summaries->flatten()->sum('total_wage');

        // Chấm công kỳ hiện tại (order by date)
        $currentAttendances = $user->attendances()
            ->whereBetween('date', [$period['start'], $period['end']])
            ->orderBy('date')
            ->get();

        // Lịch sử tổng kết — monthly: filter theo month+year, biweekly: tất cả biweekly
        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('period_start')
            ->get();

        // Tất cả chấm công (phân trang)
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
            'setting',
            'period'
        ));
    }

    // ─── SEARCH ──────────────────────────────────────────────────────────────

    public function search(Request $request)
    {
        $query = $request->input('query');
        $setting = $this->activeSetting();
        $period = $setting->getCurrentPeriod();

        $employees = Employee::with([
            'user',
            'position.salaryConfig',
            'rank',
            'userCreatedBy',
            'user.attendances' => function ($q) use ($period) {
                $q->whereBetween('date', [$period['start'], $period['end']]);
            }
        ])
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'admin'))
            ->where(function ($q) use ($query) {
                $q->where('name_ingame', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('username', 'LIKE', "%{$query}%"));
            })
            ->get();

        // Tổng kết kỳ hiện tại
        $summaryQuery = MonthlyAttendanceSummary::where('period_type', $setting->cycle_type)
            ->where('month', $period['month'])
            ->where('year', $period['year']);

        if ($setting->cycle_type === 'biweekly') {
            $summaryQuery->where('period_start', $period['period_start']);
        }

        $summaries = $summaryQuery->get()->keyBy('user_id');

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
                                'salary_config' => ['hourly_rate' => $rate]
                            ]
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
                        'total_minutes' => $summary->total_minutes ?? round($summary->total_hours * 60),
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
        $setting = $this->activeSetting();
        $prev = $setting->getPreviousPeriod();

        $payrolls = MonthlyAttendanceSummary::with(
            'user.employee.position',
            'user.employee.rank',
            'user.employee.position.salaryConfig'
        )
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'admin'))
            ->where('period_type', $setting->cycle_type)
            ->where('month', $prev['month'])
            ->where('year', $prev['year'])
            ->when($setting->cycle_type === 'biweekly', function ($q) use ($prev) {
                $q->where('period_start', $prev['period_start']);
            })
            ->get()
            ->sortBy(fn($s) => self::POSITION_ORDER[$s->user->employee?->position?->name_positions] ?? 999)
            ->values();

        return response()->json([
            'data' => $payrolls,
            'period' => $prev['label'],
        ]);
    }

    public function deletePreviousMonth()
    {
        $setting = $this->activeSetting();
        $prev = $setting->getPreviousPeriod();

        $query = MonthlyAttendanceSummary::where('period_type', $setting->cycle_type)
            ->where('month', $prev['month'])
            ->where('year', $prev['year']);

        if ($setting->cycle_type === 'biweekly') {
            $query->where('period_start', $prev['period_start']);
        }

        $query->delete();

        return response()->json(['message' => 'Đã xóa bảng lương kỳ trước (' . $prev['label'] . ')']);
    }

    // ─── CẬP NHẬT TỔNG KẾT KỲ (nội bộ) ────────────────────────────────────

    /**
     * Cập nhật MonthlyAttendanceSummary theo kỳ bất kỳ (monthly hoặc biweekly).
     *
     * $period = array từ PayrollSetting::getCurrentPeriod() hoặc tương đương.
     */
    protected function updatePeriodSummary(int $userId, array $period): MonthlyAttendanceSummary
    {
        $rows = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$period['start'], $period['end']])
            ->get();

        $totalHours = round($rows->sum('duration'), 2);
        $totalWage = (int) $rows->sum('wage');

        $criteria = [
            'user_id' => $userId,
            'period_type' => $period['type'],
            'month' => $period['month'],
            'year' => $period['year'],
        ];

        if ($period['type'] === 'biweekly') {
            $criteria['period_start'] = $period['period_start'];
        }

        return MonthlyAttendanceSummary::updateOrCreate($criteria, [
            'total_hours' => $totalHours,
            'total_wage' => $totalWage,
            'period_end' => $period['period_end'],
        ]);
    }

    /**
     * Tương thích ngược: updateMonthlySummary(userId, month, year)
     * Vẫn gọi được từ các chỗ khác trong codebase (vd: deleteAttendance, updateInline).
     * Tự lấy setting hiện tại để quyết định period.
     */
    protected function updateMonthlySummary(int $userId, int $month, int $year): MonthlyAttendanceSummary
    {
        $setting = $this->activeSetting();

        // Xác định period chứa ngày đầu của month/year truyền vào
        $refDay = Carbon::create($year, $month, 1);

        if ($setting->cycle_type === 'monthly') {
            $period = [
                'type' => 'monthly',
                'start' => $refDay->copy()->startOfMonth(),
                'end' => $refDay->copy()->endOfMonth()->endOfDay(),
                'month' => $month,
                'year' => $year,
                'period_start' => null,
                'period_end' => null,
            ];
        } else {
            $period = $setting->buildBiweeklyPeriodPublic($refDay);
        }

        return $this->updatePeriodSummary($userId, $period);
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

        $month = Carbon::parse($attendance->date)->month;
        $year = Carbon::parse($attendance->date)->year;
        $this->updateMonthlySummary($attendance->user_id, $month, $year);

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

        if ($field === 'wage') {
            $value = (int) preg_replace('/[^0-9]/', '', $value);
        }
        if ($field === 'duration') {
            $value = (float) preg_replace('/[^0-9\.]/', '', $value);
        }

        $summary = DB::transaction(function () use ($attendance, $field, $value) {
            $attendance->{$field} = $value;
            $attendance->save();

            $month = Carbon::parse($attendance->date)->month;
            $year = Carbon::parse($attendance->date)->year;
            return $this->updateMonthlySummary($attendance->user_id, $month, $year);
        });

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