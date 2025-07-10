<?php

namespace App\Http\Controllers;


use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OnDutyController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $onDutyList = Attendance::with(['user.employee.position.rank'])
            ->where('status', 'Đang On-Duty')
            ->whereDate('date', $today)
            ->get();

        return view('partials.ondutyList', compact('onDutyList'));
    }
}
