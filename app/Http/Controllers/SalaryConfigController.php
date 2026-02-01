<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Position, Rank, ActivityLog, SalaryConfig, PositionSalaryConfig, RankSalaryConfig, WorkHourConfig};

class SalaryConfigController extends Controller
{
    public function index()
    {
        // BACKUP_ Lấy Lương Theo Chức Vụ -> $configs = PositionSalaryConfig::with(['position', 'updatedBy'])->get();

        // MỚI_ Lấy Lương Theo Cấp Bậc Quân Hàm
        $configs = RankSalaryConfig::with(['rank', 'updatedBy'])->get();
        $positions = Position::all();
        $ranks = Rank::all();

        return view('salary_configs.index', compact('configs', 'positions', 'ranks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rank_id' => 'required|exists:ranks,id',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        RankSalaryConfig::updateOrCreate(
            ['rank_id' => $data['rank_id']],
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
        RankSalaryConfig::query()->update([
            'max_hours_per_day' => $request->max_hours_per_day,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật giờ làm tối đa cho toàn hệ thống.');
    }

    // BACKUP_ Lấy Lương Theo Chức Vụ
    public function store_backup_positionSLG_20012026(Request $request)
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
}
