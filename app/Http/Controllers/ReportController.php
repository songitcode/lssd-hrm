<?php

namespace App\Http\Controllers;

use App\Models\{User, Employee, Attendance, MonthlyAttendanceSummary, ActivityLog, WorkHourConfig};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // =====================================================
    //  TỔNG QUAN — Dashboard
    // =====================================================
    public function index()
    {
        // ── Chu kỳ hiện tại ──
        $config = WorkHourConfig::latestConfig();
        $period = $config->getCurrentPeriod();
        $month = $period['month'];
        $year = $period['year'];

        // ── Chỉ số kỳ này ──
        $totalEmployees = Employee::whereNull('deleted_at')->count();

        $attendancesThisPeriod = Attendance::whereBetween('date', [
            $period['start']->toDateString(),
            $period['end']->toDateString(),
        ])->get();

        $totalHoursThisMonth = round($attendancesThisPeriod->sum('duration'), 2);
        $totalWageThisMonth = $attendancesThisPeriod->sum('wage');
        $totalSessionsThisMonth = $attendancesThisPeriod->count();

        $activeEmployeesThisMonth = $attendancesThisPeriod->pluck('user_id')->unique()->count();

        $allUserIds = User::whereHas('employee')->pluck('id');
        $checkedIds = $attendancesThisPeriod->pluck('user_id')->unique();
        $absentCount = $allUserIds->diff($checkedIds)->count();

        $attendanceRate = $totalEmployees > 0
            ? round(($activeEmployeesThisMonth / $totalEmployees) * 100, 1)
            : 0;

        // ── Top 5 ──
        $topWorkers = Attendance::with(['user.employee.position', 'user.employee.rank'])
            ->whereBetween('date', [
                $period['start']->toDateString(),
                $period['end']->toDateString(),
            ])
            ->select(
                'user_id',
                DB::raw('SUM(duration) as total_hours'),
                DB::raw('SUM(wage) as total_wage'),
                DB::raw('COUNT(*) as sessions')
            )
            ->groupBy('user_id')
            ->orderByDesc('total_hours')
            ->limit(5)
            ->get();

        // ── Biểu đồ 6 kỳ gần nhất (monthly → 6 tháng, biweekly → 6 × 14 ngày) ──
        $last6Months = collect();
        foreach ($config->getLast6Periods() as $p) {
            $hrs = Attendance::whereBetween('date', [
                $p['start']->toDateString(),
                $p['end']->toDateString(),
            ])->sum('duration');
            $wage = Attendance::whereBetween('date', [
                $p['start']->toDateString(),
                $p['end']->toDateString(),
            ])->sum('wage');
            $last6Months->push([
                'label' => $p['label'],
                'hours' => round($hrs, 2),
                'wage' => (int) $wage,
            ]);
        }

        // ── Phân bổ chức vụ ──
        $byPosition = Employee::whereNull('deleted_at')
            ->with('position')->get()
            ->groupBy(fn($e) => $e->position->name_positions ?? 'Khác')
            ->map(fn($g) => $g->count());

        // ── Giờ theo ngày trong kỳ ──
        $dailyHours = Attendance::whereBetween('date', [
            $period['start']->toDateString(),
            $period['end']->toDateString(),
        ])
            ->select(DB::raw('DATE(date) as day'), DB::raw('SUM(duration) as hours'))
            ->groupBy('day')->orderBy('day')->get()
            ->mapWithKeys(fn($r) => [$r->day => round($r->hours, 2)]);

        // ── Logs ──
        $recentLogs = ActivityLog::with('user.employee')->latest()->limit(8)->get();

        return view('reports.index', compact(
            'totalEmployees',
            'totalHoursThisMonth',
            'totalWageThisMonth',
            'totalSessionsThisMonth',
            'activeEmployeesThisMonth',
            'absentCount',
            'attendanceRate',
            'topWorkers',
            'last6Months',
            'byPosition',
            'dailyHours',
            'recentLogs',
            'month',
            'year',
            'period',    // array chu kỳ — dùng $period['label'] trong view
            'config'
        ));
    }

    // =====================================================
    //  BÁO CÁO CHẤM CÔNG
    // =====================================================
    public function attendance(Request $request)
    {
        $config = WorkHourConfig::latestConfig();

        // ── Xác định date range từ request hoặc kỳ hiện tại ──
        if ($config->cycle_type === 'biweekly') {
            if ($request->filled('period_start')) {
                $start = Carbon::parse($request->period_start)->startOfDay();
                $end = Carbon::parse($request->period_end ?? $request->period_start)->addDays(13)->endOfDay();
            } else {
                $cur = $config->getCurrentPeriod();
                $start = $cur['start'];
                $end = $cur['end'];
            }
            $period = $config->buildBiweeklyFromDay($start);
            $month = $period['month'];
            $year = $period['year'];
        } else {
            $month = (int) ($request->month ?? Carbon::now()->month);
            $year = (int) ($request->year ?? Carbon::now()->year);
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
            $period = [
                'type' => 'monthly',
                'label' => 'Tháng ' . $month . '/' . $year,
                'label_prev' => 'Tháng ' . ($month === 1 ? 12 : $month - 1),
                'start' => $start,
                'end' => $end,
                'month' => $month,
                'year' => $year,
                'period_start' => null,
                'period_end' => null,
            ];
        }

        // ── Danh sách kỳ cho dropdown ──
        if ($config->cycle_type === 'biweekly') {
            $availablePeriods = MonthlyAttendanceSummary::where('period_type', 'biweekly')
                ->select('period_start', 'period_end')
                ->groupBy('period_start', 'period_end')
                ->orderByDesc('period_start')
                ->get();
            $availableMonths = collect();
        } else {
            $availableMonths = Attendance::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('YEAR(date) as year')
            )->groupBy('year', 'month')
                ->orderByDesc('year')->orderByDesc('month')
                ->get();
            $availablePeriods = collect();
        }

        $positionOrder = $this->positionOrder();
        $users = User::with(['employee.position', 'employee.rank'])
            ->whereHas('employee')->get()
            ->sort(
                fn($a, $b) =>
                ($positionOrder[$a->employee?->position?->name_positions] ?? 999)
                <=> ($positionOrder[$b->employee?->position?->name_positions] ?? 999)
            )->values();

        $summaries = [];
        foreach ($users as $u) {
            $rows = Attendance::where('user_id', $u->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get();

            $summaries[$u->id] = (object) [
                'total_hours' => round($rows->sum('duration'), 2),
                'total_wage' => (int) $rows->sum('wage'),
                'sessions' => $rows->count(),
                'avg_per_day' => $rows->count() > 0
                    ? round($rows->sum('duration') / $rows->count(), 2)
                    : 0,
            ];
        }

        // ── Export CSV ──
        if ($request->has('export')) {
            $slug = $config->cycle_type === 'biweekly'
                ? 'bw_' . str_replace(['/', ' ', '–', '—'], ['_', '_', '_', '_'], $period['label'])
                : "{$year}_{$month}";
            $filename = "attendance_{$slug}.csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users, $summaries, $period) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['STT', 'Nhân viên', 'Chức vụ', 'Tổng giờ', 'Tổng lương', 'Số phiên', 'TB/phiên', 'Kỳ']);
                $stt = 1;
                foreach ($users as $u) {
                    $s = $summaries[$u->id];
                    fputcsv($file, [
                        $stt++,
                        $u->employee?->name_ingame ?? $u->username,
                        $u->employee?->position?->name_positions ?? '',
                        $s->total_hours,
                        $s->total_wage,
                        $s->sessions,
                        $s->avg_per_day,
                        $period['label'],
                    ]);
                }
                fclose($file);
            };

            return response()->streamDownload($callback, $filename, $headers);
        }

        // ── Dữ liệu biểu đồ theo ngày ──
        $dailyData = Attendance::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(duration) as hours'),
                DB::raw('COUNT(*) as sessions')
            )
            ->groupBy('day')->orderBy('day')->get();

        $avgHourPerDay = $dailyData->avg('hours') ?? 0;

        $totalHours = collect($summaries)->sum('total_hours');
        $totalWage = collect($summaries)->sum('total_wage');
        $totalSessions = collect($summaries)->sum('sessions');
        $zeroHourCount = collect($summaries)->filter(fn($s) => $s->total_hours == 0)->count();

        $activeSummaries = collect($summaries)->filter(fn($s) => $s->total_hours > 0);
        $top3 = $activeSummaries->sortByDesc('total_hours')->take(3)->keys();
        $bot3 = $activeSummaries->sortBy('total_hours')->take(3)->keys();

        return view('reports.attendance', compact(
            'month',
            'year',
            'users',
            'summaries',
            'dailyData',
            'totalHours',
            'totalWage',
            'totalSessions',
            'zeroHourCount',
            'avgHourPerDay',
            'availableMonths',
            'availablePeriods',
            'top3',
            'bot3',
            'period',
            'config'
        ));
    }

    // =====================================================
    //  BÁO CÁO LƯƠNG
    // =====================================================
    public function payroll(Request $request)
    {
        $config = WorkHourConfig::latestConfig();

        // ── Xác định kỳ được chọn ──
        if ($config->cycle_type === 'biweekly') {
            if ($request->filled('period_start')) {
                $selStart = Carbon::parse($request->period_start)->startOfDay();
                $selEnd = Carbon::parse($request->period_end ?? $request->period_start)->addDays(13)->endOfDay();
                $selectedPeriod = $config->buildBiweeklyFromDay($selStart);
            } else {
                $selectedPeriod = $config->getCurrentPeriod();
                $selStart = $selectedPeriod['start'];
                $selEnd = $selectedPeriod['end'];
            }
        } else {
            $month = (int) ($request->month ?? Carbon::now()->month);
            $year = (int) ($request->year ?? Carbon::now()->year);
            $selStart = Carbon::create($year, $month, 1)->startOfDay();
            $selEnd = $selStart->copy()->endOfMonth()->endOfDay();
            $selectedPeriod = [
                'type' => 'monthly',
                'label' => 'Tháng ' . $month . '/' . $year,
                'start' => $selStart,
                'end' => $selEnd,
                'month' => $month,
                'year' => $year,
                'period_start' => null,
                'period_end' => null,
            ];
        }

        $month = $selectedPeriod['month'];
        $year = $selectedPeriod['year'];

        // ── Kỳ trước (so sánh) ──
        $prevPeriod = $config->cycle_type === 'biweekly'
            ? $config->buildBiweeklyFromDay(Carbon::parse($selectedPeriod['period_start'])->subDay())
            : (function () use ($month, $year, $config) {
                $pm = $month === 1 ? 12 : $month - 1;
                $py = $month === 1 ? $year - 1 : $year;
                return [
                    'label' => 'Tháng ' . $pm . '/' . $py,
                    'start' => Carbon::create($py, $pm, 1)->startOfDay(),
                    'end' => Carbon::create($py, $pm, 1)->endOfMonth()->endOfDay(),
                ];
            })();

        // ── Danh sách kỳ cho dropdown ──
        if ($config->cycle_type === 'biweekly') {
            $availablePeriods = MonthlyAttendanceSummary::where('period_type', 'biweekly')
                ->select('period_start', 'period_end', 'month', 'year')
                ->groupBy('period_start', 'period_end', 'month', 'year')
                ->orderByDesc('period_start')->get();
            $availableMonths = collect();
        } else {
            $availableMonths = MonthlyAttendanceSummary::where('period_type', 'monthly')
                ->select('month', 'year')
                ->groupBy('year', 'month')
                ->orderByDesc('year')->orderByDesc('month')->get();
            $availablePeriods = collect();
        }

        $positionOrder = $this->positionOrder();
        $users = User::with(['employee.position', 'employee.rank'])
            ->whereHas('employee')->get()
            ->sort(
                fn($a, $b) =>
                ($positionOrder[$a->employee?->position?->name_positions] ?? 999)
                <=> ($positionOrder[$b->employee?->position?->name_positions] ?? 999)
            )->values();

        $payrollData = [];
        foreach ($users as $u) {
            $rows = Attendance::where('user_id', $u->id)
                ->whereBetween('date', [$selStart->toDateString(), $selEnd->toDateString()])
                ->get();

            $prevRows = Attendance::where('user_id', $u->id)
                ->whereBetween('date', [
                    $prevPeriod['start']->toDateString(),
                    $prevPeriod['end']->toDateString(),
                ])->get();

            $curWage = (int) $rows->sum('wage');
            $prevWage = (int) $prevRows->sum('wage');
            $diff = $curWage - $prevWage;
            $diffPct = $prevWage > 0 ? round(($diff / $prevWage) * 100, 1) : null;

            $payrollData[$u->id] = (object) [
                'total_hours' => round($rows->sum('duration'), 2),
                'total_wage' => $curWage,
                'prev_wage' => $prevWage,
                'diff' => $diff,
                'diff_pct' => $diffPct,
                'rate' => $u->effectiveSalaryRate(),
                'sessions' => $rows->count(),
            ];
        }

        $totalFund = collect($payrollData)->sum('total_wage');
        $prevFund = collect($payrollData)->sum('prev_wage');
        $fundDiff = $totalFund - $prevFund;
        $fundDiffPct = $prevFund > 0 ? round(($fundDiff / $prevFund) * 100, 1) : null;

        // ── Lương theo chức vụ ──
        $wageByPosition = [];
        foreach ($users as $u) {
            $pos = $u->employee?->position?->name_positions ?? 'Khác';
            $wageByPosition[$pos] = ($wageByPosition[$pos] ?? 0) + ($payrollData[$u->id]->total_wage ?? 0);
        }

        // ── Xu hướng 6 kỳ gần nhất ──
        $fundTrend = collect();
        foreach ($config->getLast6Periods() as $p) {
            $w = Attendance::whereBetween('date', [
                $p['start']->toDateString(),
                $p['end']->toDateString(),
            ])->sum('wage');
            $fundTrend->push(['label' => $p['label'], 'wage' => (int) $w]);
        }

        // ── Export CSV ──
        if ($request->has('export')) {
            $slug = $config->cycle_type === 'biweekly'
                ? 'bw_' . str_replace(['/', ' ', '–', '—'], ['_', '_', '_', '_'], $selectedPeriod['label'])
                : "{$year}_{$month}";
            $filename = "payroll_{$slug}.csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users, $payrollData, $totalFund, $selectedPeriod, $prevPeriod) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, [
                    'STT',
                    'Nhân viên',
                    'Chức vụ',
                    'Hệ số ($/h)',
                    'Giờ làm',
                    'Lương kỳ này (' . $selectedPeriod['label'] . ')',
                    'Kỳ trước (' . $prevPeriod['label'] . ')',
                    'Chênh lệch',
                    'Chênh lệch (%)',
                ]);
                $stt = 1;
                foreach ($users as $u) {
                    $p = $payrollData[$u->id];
                    if ($p->total_wage <= 0)
                        continue;
                    fputcsv($file, [
                        $stt++,
                        $u->employee?->name_ingame ?? $u->username,
                        $u->employee?->position?->name_positions ?? '—',
                        $p->rate,
                        $p->total_hours,
                        $p->total_wage,
                        $p->prev_wage,
                        $p->diff,
                        $p->diff_pct ?? '',
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['', '', '', '', 'TỔNG', $totalFund]);
                fclose($file);
            };

            return response()->streamDownload($callback, $filename, $headers);
        }

        return view('reports.payroll', compact(
            'month',
            'year',
            'users',
            'payrollData',
            'totalFund',
            'prevFund',
            'fundDiff',
            'fundDiffPct',
            'wageByPosition',
            'fundTrend',
            'availableMonths',
            'availablePeriods',
            'selectedPeriod',
            'prevPeriod',
            'config'
        ));
    }

    // =====================================================
    //  BÁO CÁO NHÂN SỰ
    // =====================================================
    public function employees(Request $request)
    {
        $month = (int) ($request->month ?? Carbon::now()->month);
        $year = (int) ($request->year ?? Carbon::now()->year);

        // ── Chu kỳ để lọc chấm công (hoursMap, absentEmployees) ──
        $config = WorkHourConfig::latestConfig();
        $period = $config->getCurrentPeriod();

        $positionOrder = $this->positionOrder();

        $employees = Employee::whereNull('deleted_at')
            ->with(['user.attendances', 'position', 'rank', 'userCreatedBy'])
            ->get()
            ->sort(
                fn($a, $b) =>
                ($positionOrder[$a->position?->name_positions] ?? 999)
                <=> ($positionOrder[$b->position?->name_positions] ?? 999)
            )->values();

        // Gia nhập / rời đơn vị vẫn theo tháng dương lịch (vì created_at/deleted_at là sự kiện, không phụ thuộc kỳ lương)
        $newThisMonth = Employee::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['position', 'rank', 'userCreatedBy'])->get();

        $deletedThisMonth = Employee::onlyTrashed()
            ->whereMonth('deleted_at', $month)
            ->whereYear('deleted_at', $year)
            ->with(['position', 'rank'])->get();

        // Vắng và giờ làm: theo kỳ lương hiện tại (biweekly hoặc monthly)
        $checkedIds = Attendance::whereBetween('date', [
            $period['start']->toDateString(),
            $period['end']->toDateString(),
        ])->pluck('user_id')->unique();

        $absentEmployees = $employees->filter(
            fn($e) => !$checkedIds->contains($e->user_id)
        );

        $hoursMap = Attendance::whereBetween('date', [
            $period['start']->toDateString(),
            $period['end']->toDateString(),
        ])
            ->select(
                'user_id',
                DB::raw('SUM(duration) as total_hours'),
                DB::raw('COUNT(*) as sessions')
            )
            ->groupBy('user_id')->get()->keyBy('user_id');

        $byPosition = $employees->groupBy(fn($e) => $e->position?->name_positions ?? 'Khác')
            ->map(fn($g) => $g->count());

        // Tăng trưởng quân số vẫn theo tháng dương lịch
        $growthTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::create($year, $month)->subMonths($i);
            $count = Employee::whereYear('created_at', '<=', $d->year)
                ->where(
                    fn($q) =>
                    $q->whereNull('deleted_at')
                        ->orWhere(
                            fn($q2) =>
                            $q2->whereYear('deleted_at', '>=', $d->year)
                                ->whereMonth('deleted_at', '>=', $d->month)
                        )
                )->count();
            $growthTrend->push(['label' => $d->format('M Y'), 'count' => $count]);
        }

        $sessionsByPosition = [];
        foreach ($employees as $e) {
            $pos = $e->position?->name_positions ?? 'Khác';
            $hrs = $hoursMap[$e->user_id]->total_hours ?? 0;
            $sessionsByPosition[$pos] = ($sessionsByPosition[$pos] ?? 0) + round($hrs, 2);
        }

        return view('reports.employees', compact(
            'month',
            'year',
            'employees',
            'newThisMonth',
            'deletedThisMonth',
            'absentEmployees',
            'hoursMap',
            'byPosition',
            'growthTrend',
            'sessionsByPosition',
            'period',
            'config'
        ));
    }

    // ── Helper ──
    private function positionOrder(): array
    {
        return [
            'Thư Ký' => 4,
            'Đội Trưởng' => 5,
            'Đội Phó' => 6,
            'Cảnh Sát Viên' => 7,
            'Sĩ Quan Dự Bị' => 8,
            'Thực Tập' => 9,
        ];
    }
}
