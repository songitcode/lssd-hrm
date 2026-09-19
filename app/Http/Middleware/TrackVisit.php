<?php

namespace App\Http\Middleware;

use App\Services\VisitTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function __construct(private VisitTrackingService $tracking) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $this->shouldTrack($request)) {
            $this->tracking->track($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if ($request->expectsJson() || $request->ajax() || $request->is('api/*', 'heartbeat')) {
            return false;
        }

        return !$request->routeIs('admin.api.online-users');
    }
}