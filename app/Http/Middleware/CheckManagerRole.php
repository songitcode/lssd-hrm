<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // $user = auth()->user();
        $user = $request->user();

        $allowedRoles = [
            'admin',
            'trợ lý cục trưởng',
            'phó cục trưởng',
            'cục trưởng',
            'thư ký',
        ];

        $allowedPositions = [
            'Đội Trưởng',
            'Đội Phó',
        ];

        $userRole = strtolower($user->role);
        $userPosition = $user->position?->name_positions; // kiểm tra lại column name trước

        if (!in_array($userRole, $allowedRoles) && !in_array($userPosition, $allowedPositions)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
