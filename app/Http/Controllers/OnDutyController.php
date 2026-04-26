<?php

namespace App\Http\Controllers;


use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkHourConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
    public function indexLive()
    {
        return view('partials.onduty_live');
    }
    public function getOnDuty()
    {
        $list = Attendance::with([
            'user.employee.position',
            'user.employee.rank'
        ])
            ->where('status', 'Đang On-Duty')
            ->get();

        // attach discord activity
        foreach ($list as $item) {
            $discordId = $item->user->employee->discord_id ?? null;

            if ($discordId) {
                $discord = Cache::remember("discord_$discordId", 10, function () use ($discordId) {
                    return Http::get("https://api.lanyard.rest/v1/users/$discordId")->json();
                });

                $item->discord = $discord['data']['activities'] ?? [];
            } else {
                $item->discord = [];
            }
        }

        return $list;
    }
}
