<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, Attendance, SalaryConfig, WorkHourConfig, MonthlyAttendanceSummary, Position, PositionSalaryConfig, User};
use Carbon\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;
use function Psy\debug;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadMissing([
            'salaryConfig',
            'employee.rank.salaryConfig',
        ]);

        $lastMonth = Carbon::now()->subMonth();
        $this->storeMonthlySummaryIfNotExists($user->id, $lastMonth->month, $lastMonth->year);

        $today = Carbon::now()->toDateString();
        $now = Carbon::now();

        $maxHourPerDay = (float) ($user->employee->rank?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour());
        $currentMonth = $now->format('Y-m');
        // dd(($user->employee->rank?->salaryConfig?->max_hours_per_day), $user->employee->rank?->salaryConfig);

        // Auto check-out cho ca từ hôm trước
        $previousOngoing = Attendance::where('user_id', $user->id)
            ->whereNull('check_out')
            ->whereDate('date', '<', $today)
            ->first();

        if ($previousOngoing) {
            $checkIn = Carbon::parse($previousOngoing->check_in);
            $endOfDay = $checkIn->copy()->endOfDay(); // 23:59:59 của ngày check-in

            // Nếu maxHourPerDay kết thúc trước 23:59:59 thì dùng maxHourPerDay
            $maxHourCheckOut = $checkIn->copy()->addHours($maxHourPerDay);
            if ($maxHourCheckOut->lessThan($endOfDay)) {
                $checkOut = $maxHourCheckOut;
            } else {
                $checkOut = $endOfDay;
            }

            // Đảm bảo check_out không trước check_in (tránh số âm)
            if ($checkOut->lt($checkIn)) {
                $checkOut = $checkIn;
            }

            $sessionHours = $checkIn->diffInSeconds($checkOut) / 3600;
            $sessionHours = min(round($sessionHours, 2), $maxHourPerDay);
            $salaryRate = $user->employee->rank->salaryConfig->hourly_rate ?? 24000;

            $previousOngoing->update([
                'check_out' => $checkOut,
                'duration' => $sessionHours,
                'wage' => round($sessionHours * $salaryRate),
                'status' => $sessionHours >= $maxHourPerDay ? 'Đã Đạt Giới Hạn (Hệ Thống Tự Động)' : 'Hoàn Thành (Hệ Thống Tự Động)',
            ]);
        }

        // Lấy tất cả ca trong ngày hôm nay
        $todayAttendances = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('check_in', 'asc')
            ->get();

        // Tìm phiên đang mở (check_out null) trong ngày hôm nay
        $ongoing = $todayAttendances->firstWhere('check_out', null);

        // Tính tổng giờ đã làm các phiên đã đóng (có check_out) trong ngày
        $totalTodayDuration = $todayAttendances
            ->filter(fn($att) => $att->check_out !== null)
            ->sum('duration');

        // Truyền attendance history (có thể toàn bộ hoặc chỉ trong tháng)
        $attendances = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->get();

        // Có phân trang ngày
        $attendancesPaginated = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(10); // 5 ngày mỗi trang

        // Tính tổng lương ngày
        $dailySummaries = $attendancesPaginated
            ->groupBy(function ($att) {
                return Carbon::parse($att->date)->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'date' => $items->first()->date,
                    'attendances' => $items,
                    'total_wage' => $items->sum('wage'),
                    'total_duration' => $items->sum('duration'),
                ];
            });

        // Tiền lương theo tháng
        $totalLuong = $user->monthly_attendance_summaries->flatten()->sum('total_wage');
        $monthlyTotal = $attendances
            ->filter(function ($attendance) use ($now) {
                return Carbon::parse($attendance->date)->month === $now->month
                    && Carbon::parse($attendance->date)->year === $now->year;
            })
            ->sum('wage');

        //// Hoặc gọn hơn nếu query lại (hiệu suất tốt hơn):
        // $monthlyTotal = Attendance::where('user_id', $user->id)
        //     ->whereMonth('date', $now->month)
        //     ->whereYear('date', $now->year)
        //     ->sum('wage');
        ////

        $heSoLuong = $user->effectiveSalaryRate();
        // Hiển thị lịch sử tổng lương theo tháng
        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('attendance.index', compact(
            'attendances',
            'todayAttendances',
            'ongoing',
            'totalTodayDuration',
            'maxHourPerDay',
            'dailySummaries',
            'monthlyTotal',
            'totalLuong',
            'attendancesPaginated',
            'monthlySummaries',
            'heSoLuong'
        ));
    }

    public function check(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $now = Carbon::now();
        $discord_id = $user->employee->discord_id ?? null;

        // if ($discord_id === null) {
        //     return back()->with('warning', 'Hình Như Bạn Chưa Liên Kết Tài Khoản Discord');
        // }

        // Admin không được chấm công
        if ($user->role === 'admin') {
            return back()->with('warning', 'Tài khoản ADMIN không thể chấm công.');
        }

        $salaryRate = $user->employee->rank->salaryConfig->hourly_rate ?? 24000;
        $maxHourPerDay = $user->employee?->rank?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour();

        // Tổng giờ đã làm hôm nay (chỉ ca đã checkout)
        $totalTodayDuration = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_out')
            ->sum('duration');

        // Admin không được chấm công
        if ($user->role === 'admin') {
            return back()->with('warning', 'Tài khoản ADMIN không thể chấm công.');
        }

        // Tìm phiên đang mở
        $currentAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        // ===== 1. Nếu chưa có phiên → Check-in =====
        if (!$currentAttendance) {
            // Nếu đã đủ max giờ hôm nay → không cho check-in mới
            if ($totalTodayDuration >= $maxHourPerDay) {
                return back()->with('warning', "Bạn đã đạt giới hạn {$maxHourPerDay} giờ hôm nay.");
            }

            Attendance::create([
                'user_id' => $user->id,
                'check_in' => $now,
                'date' => $today,
                'status' => 'Đang On-Duty',
                'duration' => 0,
                'wage' => 0,
            ]);
            return back()->with('success', 'Bắt đầu ca thành công.');
        }

        // ===== 2. Có phiên → Check-out =====
        $checkIn = Carbon::parse($currentAttendance->check_in);
        $checkOut = $now;

        // Nếu sang ngày mới → cắt về 23:59:59 của ngày check-in
        if ($checkOut->toDateString() !== $checkIn->toDateString()) {
            $checkOut = $checkIn->copy()->setTime(23, 59, 59);
        }

        // Tổng giờ đã làm trước ca này (chỉ tính ngày check-in)
        $totalBefore = Attendance::where('user_id', $user->id)
            ->whereDate('date', $checkIn->toDateString())
            ->whereNotNull('check_out')
            ->sum('duration');

        // Thời điểm đạt max giờ trong ca này
        $secondsToMax = max(0, ($maxHourPerDay - $totalBefore) * 3600);
        $timeReachMax = $checkIn->copy()->addSeconds($secondsToMax);

        // Nếu checkout sau khi đủ giờ max → chỉ tính thời lượng đến max, nhưng giữ nguyên check_out thực tế
        if ($secondsToMax > 0 && $checkOut->greaterThanOrEqualTo($timeReachMax)) {
            $overLimit = true;
        } else {
            $overLimit = false;
        }

        // Tính giờ thực tế
        $sessionHours = round($checkIn->diffInSeconds($checkOut) / 3600, 2);
        $availableTime = max(0, $maxHourPerDay - $totalBefore);
        $finalDuration = min($sessionHours, $availableTime);
        $remainHours = round($maxHourPerDay - ($totalBefore + $finalDuration), 2);

        // 🔹 Tính giờ dư nếu người dùng checkout trễ
        $overTime = 0;
        if (($totalBefore + $sessionHours) > $maxHourPerDay) {
            $overTime = round(($totalBefore + $sessionHours) - $maxHourPerDay, 2);
        }

        $tinhTienLuong = round($finalDuration * $salaryRate);

        // ==
        if ($overTime >= 6) {
            $phanTramTru = 90;
        } elseif ($overTime >= 3) {
            $phanTramTru = 50;
        } elseif ($overTime >= 2) {
            $phanTramTru = 20;
        } else {
            $phanTramTru = 0;
        }

        $tienBiTru = round($tinhTienLuong * ($phanTramTru / 100));

        $tinhWageDu = $tinhTienLuong - $tienBiTru;
        // ==

        // Logic Hiển thị Status
        if (($totalBefore + $finalDuration) >= $maxHourPerDay) {
            // Đã đạt giới hạn
            $status = 'Đã Đạt Giới Hạn Lúc ' . $timeReachMax->format('H:i');

            // 👉 Chỉ hiển thị dư giờ + trừ tiền khi overtime >= 1h
            if ($overTime >= 1) {
                $status .= ' (Dư ' . $overTime . 'h, Trừ -'
                    . number_format($tienBiTru, 0, ',')
                    . ' (' . $phanTramTru . '%))';
            }
        } else {
            // Chưa đạt giới hạn
            $status = 'Còn ' . $remainHours . 'h';
        }

        // Cập nhật ca
        $currentAttendance->update([
            'check_out' => $checkOut, // luôn lưu thời điểm thực tế 
            'duration' => $finalDuration, // chỉ tính tối đa giờ hệ thống cho phép
            'wage' => round($tinhWageDu),
            'status' => $status,
        ]);

        return back()->with('success', 'Kết thúc ca thành công.');
    }
    public function forceCheckout($id)
    {
        $manager = Auth::user();

        if (!$manager->isManager()) {
            return redirect()->back()->with('error', 'Bạn không có quyền kết thúc ca của người khác.');
        }

        $attendance = Attendance::findOrFail($id);
        $user = $attendance->user;

        $now = Carbon::now(); // ✅ Luôn là thời gian hiện tại
        $today = $now->toDateString();

        $salaryRate = $user->employee->rank->salaryConfig->hourly_rate ?? 24000;
        $maxHourPerDay = (float) ($user->employee->rank?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour());

        // Tổng giờ đã làm hôm nay (có check_out)
        $totalTodayDuration = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_out')
            ->sum('duration');

        if (is_null($attendance->check_out)) {
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = $now; // ✅ Giờ ra là thời điểm hiện tại (không cộng giờ)

            // Tính số giờ làm việc thực tế
            $sessionHours = round($checkIn->diffInSeconds($checkOut) / 3600, 2);

            // Giới hạn giờ tối đa trong ngày (nếu cần)
            $finalDuration = min($sessionHours, $maxHourPerDay);
            $remainHours = max(0, round($maxHourPerDay - $finalDuration, 2));

            // Nếu làm vượt quá giới hạn thì tính overtime
            $overTime = 0;
            if ($sessionHours > $maxHourPerDay) {
                $overTime = round($sessionHours - $maxHourPerDay, 2);
            }

            $tinhTienLuong = round($finalDuration * $salaryRate);

            // ==
            if ($overTime >= 6) {
                $phanTramTru = 90;
            } elseif ($overTime >= 3) {
                $phanTramTru = 50;
            } elseif ($overTime >= 2) {
                $phanTramTru = 20;
            } else {
                $phanTramTru = 0;
            }

            $tienBiTru = round($tinhTienLuong * ($phanTramTru / 100));

            $tinhWageDu = $tinhTienLuong - $tienBiTru;

            // $tinhTienLuong = round($finalDuration * $salaryRate);
            // $tinhWageDu = $overTime > 24 ? round($tinhTienLuong * 0) : round($tinhTienLuong);

            $attendance->update([
                'check_out' => $checkOut,
                'duration' => $sessionHours,
                'wage' => round($tinhWageDu),
                'status' => $sessionHours >= $maxHourPerDay
                    ? 'Kết Thúc Bởi Quản Lý -' . $phanTramTru . '% (~' . number_format($tienBiTru, 0, ',') . '$ ), Vượt quá ' . $overTime . 'h'
                    : 'Kết Thúc Bởi Quản Lý (Còn ' . $remainHours . 'h)',
            ]);

            ActivityLog::create([
                'user_id' => $manager->id,
                'action' => 'logsCustom',
                'target' => $user->username,
                'detail' => 'kết thúc ca của ' . $user->employee->name_ingame . ' (ID Ca: ' . $attendance->id . '\Checkin: ' . $attendance->check_in->format(' H:i:s, d/m') . '\Checkout: ' . $checkOut->format(' H:i:s, d/m') . '\Giờ Làm: ' . $sessionHours . 'h)',
            ]);

            return redirect()->back()->with('success', 'Đã kết thúc ca của ' . $user->employee->name_ingame);
        }

        return redirect()->back()->with('warning', 'Ca này đã kết thúc trước đó.');
    }

    // $attendance->update([
    //     'check_out' => $checkOut,
    //     'duration' => $finalDuration,
    //     'wage' => round($finalDuration * $salaryRate),
    //     'status' => ($sessionHours + $finalDuration) >= $maxHourPerDay
    //         ? 'Hoàn thành - Kết thúc bảo trì'
    //         : 'Còn ' . $remainHours . 'h' . ' - Kết thúc bảo trì',
    // ]);

    public function huyCheckin($id)
    {
        $manager = Auth::user();

        if (!$manager->isManager()) {
            return redirect()->back()->with('error', 'Bạn không có quyền kết thúc ca của người khác.');
        }

        $attendance = Attendance::findOrFail($id);
        if ($attendance->check_out !== null) {
            return redirect()->back()->with('warning', 'Ca này đã kết thúc trước đó.');
        } else {
            $attendance->delete();
        }

        ActivityLog::create([
            'user_id' => $manager->id,
            'action' => 'logsCustom',
            'target' => $manager->username,
            'detail' => 'xóa công của ' . $attendance->user->employee->name_ingame . ' (ID Ca: ' . $attendance->id . '\Checkin Lúc: ' . $attendance->check_in->format(' H:i:s, d/m/Y') . ')',
        ]);

        return redirect()->back()->with('warning', 'Kết thúc ca thành công. Ca này đã bị xóa khỏi hệ thống.');
    }

    protected function storeMonthlySummaryIfNotExists($userId, $month, $year)
    {
        $firstAttendance = DB::table('attendances')
            ->where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->first();

        $lastAttendance = DB::table('attendances')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->first();

        if (!$firstAttendance || !$lastAttendance) {
            return; // Không có dữ liệu chấm công
        }

        $startDate = Carbon::parse($firstAttendance->date)->startOfMonth();
        $endDate = Carbon::parse($lastAttendance->date)->startOfMonth();

        while ($startDate->lte($endDate)) {
            $month = $startDate->month;
            $year = $startDate->year;

            // Tính tổng giờ và tổng lương cho tháng này
            $attendanceData = DB::table('attendances')
                ->where('user_id', $userId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->selectRaw('SUM(duration) as total_hours, SUM(wage) as total_wage')
                ->first();

            if ($attendanceData && ($attendanceData->total_hours > 0 || $attendanceData->total_wage > 0)) {
                MonthlyAttendanceSummary::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'total_hours' => $attendanceData->total_hours ?? 0,
                        'total_wage' => $attendanceData->total_wage ?? 0,
                    ]
                );
            }

            $startDate->addMonth();
        }
    }

    ///// XÓA LỊCH SỬ CHẤM CÔNG
    public function deleteMonthlyHistory($month, $year, User $user)
    {
        Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->delete();

        MonthlyAttendanceSummary::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->delete();

        return back()->with('success', "Đã xóa lịch sử tháng $month/$year của {$user->name}");
    }

    /////
    public function updateMonthlySummary($userId, $month, $year)
    {
        $attendances = Attendance::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalHours = $attendances->sum('duration');
        $totalWage = $attendances->sum('wage');

        MonthlyAttendanceSummary::updateOrCreate(
            ['user_id' => $userId, 'month' => $month, 'year' => $year],
            ['total_hours' => $totalHours, 'total_wage' => $totalWage]
        );
    }

    public function resetAttendanceDta()
    {
        $user = Auth::user();

        if (!$user->isDownAdminRole()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        if (!Attendance::exists()) {
            return back()->with('warning', 'Dữ liệu chấm công đã được reset trước đó.');
        }

        DB::beginTransaction();

        try {
            Attendance::query()->delete();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logsCustom',
                'target' => $user->username,
                'detail' => 'Reset dữ liệu chấm công hệ thống',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('success', 'Đã reset toàn bộ dữ liệu chấm công.');
    }

    public function index_backup_200102026_185500()
    {
        $user = Auth::user()->loadMissing([
            'salaryConfig',
            'employee.position.salaryConfig',
        ]);

        // $user = Auth::user();

        $lastMonth = Carbon::now()->subMonth();
        $this->storeMonthlySummaryIfNotExists($user->id, $lastMonth->month, $lastMonth->year);

        $today = Carbon::now()->toDateString();
        $now = Carbon::now();
        // $maxHourPerDay = WorkHourConfig::currentMaxHour(); // ví dụ 3.0
        $maxHourPerDay = (float) ($user->position?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour());
        $currentMonth = $now->format('Y-m');

        // ✅ Auto check-out cho ca từ hôm trước
        $previousOngoing = Attendance::where('user_id', $user->id)
            ->whereNull('check_out')
            ->whereDate('date', '<', $today)
            ->first();

        if ($previousOngoing) {
            $checkIn = Carbon::parse($previousOngoing->check_in);
            $endOfDay = $checkIn->copy()->endOfDay(); // 23:59:59 của ngày check-in

            // Nếu maxHourPerDay kết thúc trước 23:59:59 thì dùng maxHourPerDay
            $maxHourCheckOut = $checkIn->copy()->addHours($maxHourPerDay);
            if ($maxHourCheckOut->lessThan($endOfDay)) {
                $checkOut = $maxHourCheckOut;
            } else {
                $checkOut = $endOfDay;
            }

            // 🔹 Đảm bảo check_out không trước check_in (tránh số âm)
            if ($checkOut->lt($checkIn)) {
                $checkOut = $checkIn;
            }

            $sessionHours = $checkIn->diffInSeconds($checkOut) / 3600;
            $sessionHours = min(round($sessionHours, 2), $maxHourPerDay);

            $salaryRate = $user->position->salaryConfig->hourly_rate ?? 24000;

            $previousOngoing->update([
                'check_out' => $checkOut,
                'duration' => $sessionHours,
                'wage' => round($sessionHours * $salaryRate),
                'status' => $sessionHours >= $maxHourPerDay ? 'Đã Đạt Giới Hạn (Hệ Thống Tự Động)' : 'Hoàn Thành (Hệ Thống Tự Động)',
            ]);
        }

        // Lấy tất cả ca trong ngày hôm nay
        $todayAttendances = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('check_in', 'asc')
            ->get();

        // Tìm phiên đang mở (check_out null) trong ngày hôm nay
        $ongoing = $todayAttendances->firstWhere('check_out', null);

        // Tính tổng giờ đã làm các phiên đã đóng (có check_out) trong ngày
        $totalTodayDuration = $todayAttendances
            ->filter(fn($att) => $att->check_out !== null)
            ->sum('duration');

        // Truyền attendance history (có thể toàn bộ hoặc chỉ trong tháng)
        $attendances = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->get();

        // Có phân trang ngày
        $attendancesPaginated = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(10); // 5 ngày mỗi trang

        // Tính tổng lương ngày
        $dailySummaries = $attendancesPaginated
            ->groupBy(function ($att) {
                return Carbon::parse($att->date)->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'date' => $items->first()->date,
                    'attendances' => $items,
                    'total_wage' => $items->sum('wage'),
                    'total_duration' => $items->sum('duration'),
                ];
            });

        $totalLuong = $user->monthly_attendance_summaries->flatten()->sum('total_wage');
        $monthlyTotal = $attendances
            ->filter(function ($attendance) use ($now) {
                return Carbon::parse($attendance->date)->month === $now->month
                    && Carbon::parse($attendance->date)->year === $now->year;
            })
            ->sum('wage');

        $heSoLuong = $user->effectiveSalaryRate();
        // Hiển thị lịch sử tổng lương theo tháng
        $monthlySummaries = MonthlyAttendanceSummary::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('attendance.index', compact(
            'attendances',
            'todayAttendances',
            'ongoing',
            'totalTodayDuration',
            'maxHourPerDay',
            'dailySummaries',
            'monthlyTotal',
            'totalLuong',
            'attendancesPaginated',
            'monthlySummaries',
            'heSoLuong'
        ));
    }
}


// CHECK FUNCTION BACK_KUP NOT tienBiTru
function check_BACKUP(Request $request)
{
    $user = Auth::user();
    $today = Carbon::now()->toDateString();
    $now = Carbon::now();

    $salaryRate = $user->position->salaryConfig->hourly_rate ?? 24000;
    $maxHourPerDay = $user->position?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour();

    $totalTodayDuration = Attendance::where('user_id', $user->id)
        ->whereDate('date', $today)
        ->whereNotNull('check_out')
        ->sum('duration');

    if ($user->role === 'admin') {
        return back()->with('warning', 'Tài khoản ADMIN không thể chấm công.');
    }

    $currentAttendance = Attendance::where('user_id', $user->id)
        ->whereNull('check_out')
        ->latest('check_in')
        ->first();

    if (!$currentAttendance) {
        if ($totalTodayDuration >= $maxHourPerDay) {
            return back()->with('warning', "Bạn đã đạt giới hạn {$maxHourPerDay} giờ hôm nay.");
        }

        Attendance::create([
            'user_id' => $user->id,
            'check_in' => $now,
            'date' => $today,
            'status' => 'Đang On-Duty',
            'duration' => 0,
            'wage' => 0,
        ]);
        return back()->with('success', 'Bắt đầu ca thành công.');
    }

    $checkIn = Carbon::parse($currentAttendance->check_in);
    $checkOut = $now;

    if ($checkOut->toDateString() !== $checkIn->toDateString()) {
        $checkOut = $checkIn->copy()->setTime(23, 59, 59);
    }

    $totalBefore = Attendance::where('user_id', $user->id)
        ->whereDate('date', $checkIn->toDateString())
        ->whereNotNull('check_out')
        ->sum('duration');

    $secondsToMax = max(0, ($maxHourPerDay - $totalBefore) * 3600);
    $timeReachMax = $checkIn->copy()->addSeconds($secondsToMax);

    if ($secondsToMax > 0 && $checkOut->greaterThanOrEqualTo($timeReachMax)) {
        $overLimit = true;
    } else {
        $overLimit = false;
    }

    $sessionHours = round($checkIn->diffInSeconds($checkOut) / 3600, 2);
    $availableTime = max(0, $maxHourPerDay - $totalBefore);
    $finalDuration = min($sessionHours, $availableTime);
    $remainHours = round($maxHourPerDay - ($totalBefore + $finalDuration), 2);

    $overTime = 0;
    if (($totalBefore + $sessionHours) > $maxHourPerDay) {
        $overTime = round(($totalBefore + $sessionHours) - $maxHourPerDay, 2);
    }

    if (($totalBefore + $finalDuration) >= $maxHourPerDay) {
        $status = 'Đã Đạt Giới Hạn Lúc ' . $timeReachMax->format('H:i');

        if ($overTime >= 1) {
            $status .= ' (Dư ' . $overTime . 'h)';
        }
    } else {
        $status = 'Còn ' . $remainHours . 'h';
    }

    $currentAttendance->update([
        'check_out' => $checkOut,
        'duration' => $finalDuration,
        'wage' => round($finalDuration * $salaryRate),
        'status' => $status,
    ]);

    return back()->with('success', 'Kết thúc ca thành công.');
}