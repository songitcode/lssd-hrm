<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $allowedRoles = [
            'admin',
            'trợ lý cục trưởng',
            'phó cục trưởng',
            'cục trưởng',
            'thư ký',
        ];

        if (!$user || !in_array(strtolower($user->role), $allowedRoles)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
