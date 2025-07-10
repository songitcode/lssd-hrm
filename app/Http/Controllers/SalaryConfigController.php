<?php

namespace App\Http\Controllers;

use App\Models\SalaryConfig;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\{Position, Rank, PositionSalaryConfig, WorkHourConfig};

class SalaryConfigController extends Controller
{
    public function index()
    {
        $configs = PositionSalaryConfig::with(['position', 'updatedBy'])->get();
        $positions = Position::all();

        return view('salary_configs.index', compact('configs', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        PositionSalaryConfig::updateOrCreate(
            ['position_id' => $data['position_id']],
            [
                'hourly_rate' => $data['hourly_rate'],
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Cập nhật hệ số thành công!');
    }

    public function updateGlobalHours(Request $request)
    {
        $request->validate([
            'max_hours_per_day' => 'required|numeric|min:0|max:24',
        ]);

        // Cập nhật toàn bộ configs
        PositionSalaryConfig::query()->update([
            'max_hours_per_day' => $request->max_hours_per_day,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật giờ làm tối đa cho toàn hệ thống.');
    }


}
