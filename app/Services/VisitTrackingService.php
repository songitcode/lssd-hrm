<?php

namespace App\Services;

use App\Models\DailyOnlineStat;
use App\Models\VisitorSession;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class VisitTrackingService
{
    public const ONLINE_MINUTES = 5;
    private ?bool $storageAvailable = null;

    public function track(Request $request): void
    {
        if (!$this->storageIsAvailable()) {
            return;
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        if (!$sessionId) {
            return;
        }

        $this->touchSession($request, $sessionId);

        $key = 'visit:' . sha1($sessionId . '|' . $request->path());
        if (Cache::has($key)) {
            return;
        }

        Visit::create([
            'session_id' => $sessionId,
            'user_id' => $request->user()?->getAuthIdentifier(),
            'ip_address' => $request->ip(),
            'url' => $request->url(),
            'route_name' => $request->route()?->getName(),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->deviceType($request->userAgent()),
            'browser' => $this->browser($request->userAgent()),
        ]);

        Cache::put($key, true, now()->addMinute());
    }

    public function heartbeat(Request $request): array
    {
        if (!$this->storageIsAvailable()) {
            return ['online_count' => 0, 'peak_online' => 0, 'users' => []];
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        if (!$sessionId) {
            return $this->onlineSnapshot();
        }

        $this->touchSession($request, $sessionId);
        return $this->onlineSnapshot();
    }

    public function onlineSnapshot(): array
    {
        if (!$this->storageIsAvailable()) {
            return ['online_count' => 0, 'peak_online' => 0, 'users' => []];
        }

        $cutoff = now()->subMinutes(self::ONLINE_MINUTES);
        $query = VisitorSession::where('last_activity', '>=', $cutoff);
        $onlineCount = (clone $query)->whereNotNull('user_id')->distinct('user_id')->count('user_id')
            + (clone $query)->whereNull('user_id')->count();

        $peak = DailyOnlineStat::firstOrCreate(['date' => today()], ['peak_online' => 0]);
        if ($onlineCount > $peak->peak_online) {
            $peak->update(['peak_online' => $onlineCount]);
        }

        $users = VisitorSession::with(['user.employee.position', 'user.employee.rank'])
            ->where('last_activity', '>=', $cutoff)
            ->whereNotNull('user_id')
            ->orderByDesc('last_activity')
            ->get()
            ->unique('user_id')
            ->values()
            ->map(fn (VisitorSession $session) => [
                'id' => $session->user_id,
                'name' => $session->user?->employee?->name_ingame ?? $session->user?->username ?? 'Unknown',
                'role' => $session->user?->employee?->position?->name_positions
                    ?? $session->user?->role ?? '',
                'avatar' => $session->user?->employee?->avatar
                    ? asset('storage/' . $session->user->employee->avatar)
                    : null,
                'last_activity' => $session->last_activity?->diffForHumans(),
                'last_activity_iso' => $session->last_activity?->toIso8601String(),
            ]);

        return ['online_count' => $onlineCount, 'peak_online' => $peak->peak_online, 'users' => $users];
    }

    public function dashboardData(): array
    {
        if (!$this->storageIsAvailable()) {
            return [
                'totalVisits' => 0, 'todayVisits' => 0, 'monthVisits' => 0,
                'topPages' => collect(), 'chart' => collect(), 'rangeDays' => 7,
                'online' => ['online_count' => 0, 'peak_online' => 0, 'users' => []],
            ];
        }

        $today = today();
        $monthStart = now()->startOfMonth();
        $days = request()->integer('days', 7) === 30 ? 30 : 7;
        $from = now()->subDays($days - 1)->startOfDay();

        $daily = Visit::query()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = collect(range(0, $days - 1))->map(function (int $offset) use ($from, $daily) {
            $date = $from->copy()->addDays($offset);
            return ['label' => $date->format('d/m'), 'date' => $date->toDateString(), 'total' => (int) ($daily[$date->toDateString()] ?? 0)];
        });

        return [
            'totalVisits' => Visit::count(),
            'todayVisits' => Visit::whereDate('created_at', $today)->count(),
            'monthVisits' => Visit::where('created_at', '>=', $monthStart)->count(),
            'topPages' => Visit::query()
                ->select('url', 'route_name')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('url', 'route_name')
                ->orderByDesc('total')
                ->limit(8)
                ->get(),
            'chart' => $chart,
            'rangeDays' => $days,
            'online' => $this->onlineSnapshot(),
        ];
    }

    private function touchSession(Request $request, string $sessionId): void
    {
        VisitorSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $request->user()?->getAuthIdentifier(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->deviceType($request->userAgent()),
                'browser' => $this->browser($request->userAgent()),
                'last_activity' => now(),
            ]
        );
    }

    private function storageIsAvailable(): bool
    {
        return $this->storageAvailable ??= Schema::hasTable('visits')
            && Schema::hasTable('visitor_sessions')
            && Schema::hasTable('daily_online_stats');
    }

    private function deviceType(?string $userAgent): string
    {
        $ua = strtolower($userAgent ?? '');
        return str_contains($ua, 'ipad') || str_contains($ua, 'tablet')
            ? 'tablet'
            : (preg_match('/mobile|android|iphone|ipod/', $ua) ? 'mobile' : 'desktop');
    }

    private function browser(?string $userAgent): string
    {
        $ua = $userAgent ?? '';
        return match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Other',
        };
    }
}