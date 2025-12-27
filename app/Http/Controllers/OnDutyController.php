<?php

namespace App\Http\Controllers;


use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkHourConfig;

class OnDutyController extends Controller
{
    public function index()
    {
        // $today = Carbon::today();
        // $today = Carbon::now()->toDateString();
        // $user = Auth::user()->loadMissing([
        //     'salaryConfig',
        //     'employee.position.salaryConfig',
        // ]);

        $onDutyList = Attendance::with(['user.employee.position.rank'])
            ->where('status', 'Đang On-Duty')
            // ->whereDate('date', $today)
            ->get();
        // $maxHourPerDay = (float) ($user->position?->salaryConfig?->max_hours_per_day ?? WorkHourConfig::currentMaxHour());

        // $todayAttendances = Attendance::where('user_id', $user->id)
        //     ->whereDate('date', $today)
        //     ->orderBy('check_in', 'asc')
        //     ->get();
        // $totalTodayDuration = $todayAttendances
        //     ->filter(fn($att) => $att->check_out !== null)
        //     ->sum('duration');

        // $isFullHour = $totalTodayDuration > $maxHourPerDay;

        return view('partials.ondutyList', compact('onDutyList'));
    }
}
