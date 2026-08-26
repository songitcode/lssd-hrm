<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user.employee')
            ->latest()
            ->paginate(50);

        return view('activity_logs.index', compact('logs'));
    }
}
