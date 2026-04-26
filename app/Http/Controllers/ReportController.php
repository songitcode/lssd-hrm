<?php

namespace App\Http\Controllers;

use App\Models\{User, Employee, Attendance, MonthlyAttendanceSummary, ActivityLog};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // =====================================================
    //  TỔNG QUAN — Dashboard chính của module báo cáo
    // =====================================================
    public function index()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // ── Chỉ số tháng này ──
        $totalEmployees = Employee::whereNull('deleted_at')->count();

        $attendancesThisMonth = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)->get();

        $totalHoursThisMonth = round($attendancesThisMonth->sum('duration'), 2);
        $totalWageThisMonth = $attendancesThisMonth->sum('wage');
        $totalSessionsThisMonth = $attendancesThisMonth->count();

        // Nhân viên đã chấm công ít nhất 1 lần trong tháng
        $activeEmployeesThisMonth = $attendancesThisMonth->pluck('user_id')->unique()->count();

        // Nhân viên chưa chấm công tháng này
        $allUserIds = User::whereHas('employee')->pluck('id');
        $checkedIds = $attendancesThisMonth->pluck('user_id')->unique();
        $absentCount = $allUserIds->diff($checkedIds)->count();

        // ── Tỉ lệ chấm công ──
        $attendanceRate = $totalEmployees > 0
            ? round(($activeEmployeesThisMonth / $totalEmployees) * 100, 1)
            : 0;

        // ── Top 5 nhân viên nhiều giờ nhất tháng này ──
        $topWorkers = Attendance::with(['user.employee.position', 'user.employee.rank'])
            ->whereMonth('date', $month)->whereYear('date', $year)
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

        // ── Biểu đồ: Tổng giờ làm 6 tháng gần nhất ──
        $last6Months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $hrs = Attendance::whereMonth('date', $d->month)
                ->whereYear('date', $d->year)
                ->sum('duration');
            $wage = Attendance::whereMonth('date', $d->month)
                ->whereYear('date', $d->year)
                ->sum('wage');
            $last6Months->push([
                'label' => $d->format('M Y'),
                'hours' => round($hrs, 2),
                'wage' => (int) $wage,
            ]);
        }

        // ── Biểu đồ: Phân bổ nhân sự theo chức vụ ──
        $byPosition = Employee::whereNull('deleted_at')
            ->with('position')
            ->get()
            ->groupBy(fn($e) => $e->position->name_positions ?? 'Khác')
            ->map(fn($g) => $g->count());

        // ── Biểu đồ: Giờ làm theo từng ngày trong tháng ──
        $dailyHours = Attendance::whereMonth('date', $month)->whereYear('date', $year)
            ->select(DB::raw('DATE(date) as day'), DB::raw('SUM(duration) as hours'))
            ->groupBy('day')->orderBy('day')->get()
            ->mapWithKeys(fn($r) => [$r->day => round($r->hours, 2)]);

        // ── Hoạt động gần nhất (logs) ──
        $recentLogs = ActivityLog::with('user.employee')
            ->latest()->limit(8)->get();

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
            'year'
        ));
    }

    // =====================================================
    //  BÁO CÁO CHẤM CÔNG — Chi tiết theo tháng
    // =====================================================
    public function attendance(Request $request)
    {
        $month = (int) ($request->month ?? Carbon::now()->month);
        $year = (int) ($request->year ?? Carbon::now()->year);

        $availableMonths = Attendance::select(
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year')
        )->groupBy('year', 'month')
            ->orderByDesc('year')->orderByDesc('month')
            ->get();

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
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $summaries[$u->id] = (object) [
                'total_hours' => round($rows->sum('duration'), 2),
                'total_wage' => (int) $rows->sum('wage'),
                'sessions' => $rows->count(),
                'avg_per_day' => $rows->count() > 0 ? round($rows->sum('duration') / $rows->count(), 2) : 0,
            ];
        }

        // ✅ Nếu export thì trả về file CSV
        if ($request->has('export')) {
            $filename = "attendance_{$year}_{$month}.csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users, $summaries, $month, $year) {
                $file = fopen('php://output', 'w');

                // BOM để Excel đọc tiếng Việt tốt hơn
                fwrite($file, "\xEF\xBB\xBF");

                // Header CSV
                fputcsv($file, ['STT', 'Nhân viên', 'Chức vụ', 'Tổng giờ', 'Tổng lương', 'Số phiên', 'TB / phiên']);

                $stt = 1;
                foreach ($users as $u) {
                    $summary = $summaries[$u->id];

                    fputcsv($file, [
                        $stt++,
                        $u->name ?? $u->employee?->name_ingame ?? 'N/A',
                        $u->employee->position->name_positions ?? '',
                        $summary->total_hours,
                        $summary->total_wage,
                        $summary->sessions,
                        $summary->avg_per_day,
                    ]);
                }

                fclose($file);
            };

            return response()->streamDownload($callback, $filename, $headers);
        }

        $dailyData = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(duration) as hours'),
                DB::raw('COUNT(*) as sessions')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

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
            'top3',
            'bot3'
        ));
    }

    // =====================================================
    //  BÁO CÁO LƯƠNG — So sánh tháng, quỹ lương
    // =====================================================
    public function payroll(Request $request)
    {
        $month = (int) ($request->month ?? Carbon::now()->month);
        $year = (int) ($request->year ?? Carbon::now()->year);

        $availableMonths = MonthlyAttendanceSummary::select('month', 'year')
            ->groupBy('year', 'month')
            ->orderByDesc('year')->orderByDesc('month')->get();

        $positionOrder = $this->positionOrder();

        // Dữ liệu lương tháng được chọn (từ bảng tổng hợp tháng trước / trực tiếp từ attendance)
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
                ->whereMonth('date', $month)->whereYear('date', $year)->get();
            // So sánh với tháng trước
            $prevMonth = $month == 1 ? 12 : $month - 1;
            $prevYear = $month == 1 ? $year - 1 : $year;
            $prevRows = Attendance::where('user_id', $u->id)
                ->whereMonth('date', $prevMonth)->whereYear('date', $prevYear)->get();

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

        // Tổng quỹ lương
        $totalFund = collect($payrollData)->sum('total_wage');
        $prevFund = collect($payrollData)->sum('prev_wage');
        $fundDiff = $totalFund - $prevFund;
        $fundDiffPct = $prevFund > 0 ? round(($fundDiff / $prevFund) * 100, 1) : null;

        // Biểu đồ lương theo chức vụ
        $wageByPosition = [];
        foreach ($users as $u) {
            $pos = $u->employee?->position?->name_positions ?? 'Khác';
            $wage = $payrollData[$u->id]->total_wage ?? 0;
            $wageByPosition[$pos] = ($wageByPosition[$pos] ?? 0) + $wage;
        }

        // Biểu đồ quỹ lương 6 tháng
        $fundTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::create($year, $month)->subMonths($i);
            $w = Attendance::whereMonth('date', $d->month)
                ->whereYear('date', $d->year)->sum('wage');
            $fundTrend->push(['label' => $d->format('M Y'), 'wage' => (int) $w]);
        }

        // ✅ Nếu export thì trả về file CSV
        if ($request->has('export')) {
            $filename = "payroll_{$year}_{$month}.csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users, $payrollData, $totalFund) {
                $file = fopen('php://output', 'w');

                // BOM để Excel đọc tiếng Việt chuẩn
                fwrite($file, "\xEF\xBB\xBF");

                // Header
                fputcsv($file, [
                    'STT',
                    'Nhân viên',
                    'Chức vụ',
                    'Hệ số ($/h)',
                    'Giờ làm',
                    'Lương tháng này',
                    'Tháng trước',
                    'Chênh lệch',
                    'Chênh lệch (%)',
                ]);

                $stt = 1;
                foreach ($users as $u) {
                    $p = $payrollData[$u->id];

                    // ❌ Bỏ qua người không có lương
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
                        $p->diff_pct !== null ? $p->diff_pct : '',
                    ]);
                }

                // Thêm dòng trống trước tổng
                fputcsv($file, []);
                // ✅ Tổng lương
                fputcsv($file, [
                    '',
                    '',
                    '',
                    '',
                    'TỔNG LƯƠNG THÁNG',
                    $totalFund,
                ]);

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
            'availableMonths'
        ));
    }

    // =====================================================
    //  BÁO CÁO NHÂN SỰ — Hoạt động, tăng giảm quân số
    // =====================================================
    public function employees(Request $request)
    {
        $month = (int) ($request->month ?? Carbon::now()->month);
        $year = (int) ($request->year ?? Carbon::now()->year);

        $positionOrder = $this->positionOrder();

        // Toàn bộ nhân viên active
        $employees = Employee::whereNull('deleted_at')
            ->with(['user.attendances', 'position', 'rank', 'userCreatedBy'])
            ->get()
            ->sort(
                fn($a, $b) =>
                ($positionOrder[$a->position?->name_positions] ?? 999)
                <=> ($positionOrder[$b->position?->name_positions] ?? 999)
            )->values();

        // Nhân viên mới (gia nhập trong tháng được chọn)
        $newThisMonth = Employee::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['position', 'rank', 'userCreatedBy'])->get();

        // Nhân viên đã xóa trong tháng (soft delete)
        $deletedThisMonth = Employee::onlyTrashed()
            ->whereMonth('deleted_at', $month)
            ->whereYear('deleted_at', $year)
            ->with(['position', 'rank'])->get();

        // Nhân viên chưa chấm công trong tháng
        $checkedIds = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)->pluck('user_id')->unique();
        $absentEmployees = $employees->filter(
            fn($e) => !$checkedIds->contains($e->user_id)
        );

        // Giờ làm của từng nhân viên trong tháng
        $hoursMap = Attendance::whereMonth('date', $month)->whereYear('date', $year)
            ->select('user_id', DB::raw('SUM(duration) as total_hours'), DB::raw('COUNT(*) as sessions'))
            ->groupBy('user_id')->get()->keyBy('user_id');

        // Tổng nhân sự theo chức vụ
        $byPosition = $employees->groupBy(fn($e) => $e->position?->name_positions ?? 'Khác')
            ->map(fn($g) => $g->count());

        // Biểu đồ tăng trưởng nhân sự 6 tháng
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
            $growthTrend->push([
                'label' => $d->format('M Y'),
                'count' => $count,
            ]);
        }

        // Tổng hoạt động (sessions) theo chức vụ trong tháng
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
            'sessionsByPosition'
        ));
    }

    // ── Helper ──
    private function positionOrder(): array
    {
        return [
            // 'Cục Trưởng' => 1,
            // 'Phó Cục Trưởng' => 2,
            // 'Trợ Lý Cục Trưởng' => 3,
            'Thư Ký' => 4,
            'Đội Trưởng' => 5,
            'Đội Phó' => 6,
            'Cảnh Sát Viên' => 7,
            'Sĩ Quan Dự Bị' => 8,
            'Thực Tập' => 9,
        ];
    }
}
