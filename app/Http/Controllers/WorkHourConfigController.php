<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkHourConfig;

class WorkHourConfigController extends Controller
{
    public function index()
    {
        $latest = WorkHourConfig::latest()->first();
        return view('partials.work-hour-config', compact('latest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'max_hours_per_day' => 'required|numeric|min:0|max:24',
        ]);

        WorkHourConfig::create([
            'max_hours_per_day' => $request->max_hours_per_day,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Đã cập nhật giờ làm tối đa.');
    }
}
