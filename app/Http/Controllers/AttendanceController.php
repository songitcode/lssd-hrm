<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, SalaryConfig, WorkHourConfig, MonthlyAttendanceSummary, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index()
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

        // ✅ Auto check-out cho phiên treo từ hôm trước
        $previousOngoing = Attendance::where('user_id', $user->id)
            ->whereNull('check_out')
            ->whereDate('date', '<', $today)
            ->first();

        if ($previousOngoing) {
            $checkIn = Carbon::parse($previousOngoing->check_in);
            $autoCheckOut = $checkIn->copy()->addHours($maxHourPerDay);

            $sessionHours = $checkIn->diffInSeconds($autoCheckOut) / 3600;
            $sessionHours = min(round($sessionHours, 2), $maxHourPerDay);
            $salaryRate = $user->position->salaryConfig->hourly_rate ?? 24000;
            // $salaryRate = $user->employee->effectiveSalaryRate();


            $previousOngoing->update([
                'check_out' => $autoCheckOut,
                'duration' => $sessionHours,
                'wage' => round($sessionHours * $salaryRate),
                'status' => 'Hoàn thành',
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
        $totalLuong = $attendances->flatten()->sum('wage');
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

        $salaryRate = $user->position->salaryConfig->hourly_rate ?? 24000;
        // $salaryRate = $user->employee->effectiveSalaryRate();
        // $maxHourPerDay = WorkHourConfig::currentMaxHour();
        $maxHourPerDay = $user->position?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour(); // 

        // Tổng giờ đã làm hôm nay
        $totalTodayDuration = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_out')
            ->sum('duration');

        // Nếu đã đạt giới hạn giờ, ngăn chấm công
        if ($totalTodayDuration >= $maxHourPerDay) {
            return redirect()->back()->with('warning', 'Bạn đã đạt giới hạn ' . $maxHourPerDay . ' giờ hôm nay.');
        }
        if ($user->role === 'admin') {
            return redirect()->back()->with('warning', 'Bạn không thể thực hiện hành động này với tài khoản ADMIN.');
        }
        // Tìm phiên đang mở
        $currentAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (!$currentAttendance) {
            // Check-in: tạo phiên mới
            Attendance::create([
                'user_id' => $user->id,
                'check_in' => $now,
                'date' => $today,
                'status' => 'Đang On-Duty',
                'duration' => 0,
                'wage' => 0,
            ]);
            return redirect()->back()->with('success', 'Bắt đầu ca thành công.');
        } else {
            // Check-out: đóng phiên hiện tại
            $checkIn = Carbon::parse($currentAttendance->check_in);
            $checkOut = $now;

            // Nếu checkOut qua ngày khác, fix duration = maxHourPerDay
            if ($checkOut->toDateString() !== $checkIn->toDateString()) {
                $checkOut = $checkIn->copy()->addHours($maxHourPerDay);
            }

            $sessionHours = $checkIn->diffInSeconds($checkOut) / 3600;
            $sessionHours = min(round($sessionHours, 2), $maxHourPerDay);

            // Tính lại tổng cũ
            $totalBefore = $totalTodayDuration;
            $availableTime = max(0, $maxHourPerDay - $totalBefore);
            $finalDuration = min($sessionHours, $availableTime);
            // $remainHours = number_format($maxHourPerDay - ($totalBefore + $finalDuration), 2);
            $remainHours = round($maxHourPerDay - ($totalBefore + $finalDuration), 2);
            // $this->storeMonthlySummaryIfNotExists($user->id, $checkOut->month, $checkOut->year);

            $currentAttendance->update([
                'check_out' => $checkOut,
                'duration' => $finalDuration,
                'wage' => round($finalDuration * $salaryRate),
                'status' => ($totalBefore + $finalDuration) >= $maxHourPerDay
                    ? 'Hoàn thành'
                    : 'Còn ' . $remainHours . 'h',
            ]);

            return redirect()->back()->with('success', 'Kết thúc ca thành công.');
        }
    }
    protected function storeMonthlySummaryIfNotExists($userId, $month, $year)
    {
        $exists = MonthlyAttendanceSummary::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if (!$exists) {
            $attendances = Attendance::where('user_id', $userId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $totalHours = $attendances->sum('duration');
            $totalWage = $attendances->sum('wage');

            MonthlyAttendanceSummary::create([
                'user_id' => $userId,
                'month' => $month,
                'year' => $year,
                'total_hours' => $totalHours,
                'total_wage' => $totalWage,
            ]);
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


    public function resetAll()
    {
        // Kiểm tra quyền hạn nếu cần (admin, supervisor,...)
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Xóa toàn bộ bản ghi chấm công và tổng kết lương tháng
        Attendance::truncate();
        MonthlyAttendanceSummary::truncate();

        return back()->with('success', 'Đã reset toàn bộ dữ liệu chấm công.');
    }

}
