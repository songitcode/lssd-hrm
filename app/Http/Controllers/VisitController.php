<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\VisitTrackingService;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function __construct(private VisitTrackingService $tracking) {}

    public function heartbeat(Request $request)
    {
        return response()->json(['success' => true] + $this->tracking->heartbeat($request));
    }

    public function onlineUsers()
    {
        $snapshot = $this->tracking->onlineSnapshot();
        return response()->json(['success' => true] + $snapshot);
    }

    public function index(Request $request)
    {
        $query = Visit::with(['user.employee.position', 'user.employee.rank'])->latest();

        match ($request->input('period')) {
            'today' => $query->whereDate('created_at', today()),
            '7d' => $query->where('created_at', '>=', now()->subDays(7)),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)),
            default => null,
        };

        $query->when($request->filled('user'), function ($query) use ($request) {
            $value = $request->input('user');
            $query->where(function ($query) use ($value) {
                $query->whereHas('user', fn ($user) => $user->where('username', 'like', "%{$value}%"))
                    ->orWhereHas('user.employee', fn ($employee) => $employee->where('name_ingame', 'like', "%{$value}%"));
            });
        });
        $query->when($request->filled('url'), fn ($query) => $query->where('url', 'like', '%' . $request->input('url') . '%'));
        $query->when($request->input('visitor') === 'guest', fn ($query) => $query->whereNull('user_id'));
        $query->when($request->input('visitor') === 'user', fn ($query) => $query->whereNotNull('user_id'));

        $visits = $query->paginate(25)->withQueryString();
        return view('visits.index', compact('visits'));
    }
}