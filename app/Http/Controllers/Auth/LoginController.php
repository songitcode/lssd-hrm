<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    //
    public function showLoginForm()
    {
        $contacts = User::with('employee.position')
            ->whereHas('employee.position', function ($query) {
                $query->whereIn('name_positions', [
                    'Cục Trưởng',
                    'Phó Cục Trưởng',
                    'Trợ Lý Cục Trưởng',
                    'Thư Ký',
                ]);
            })
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->join('positions', 'employees.position_id', '=', 'positions.id')
            ->orderByRaw("
            FIELD(positions.name_positions, 
                'Cục Trưởng', 
                'Phó Cục Trưởng', 
                'Trợ Lý Cục Trưởng', 
                'Thư Ký'
            )
        ")
            ->select('users.*') // tránh trùng cột
            ->get();

        return view('auth.login', compact('contacts'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
        }

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', "Đây không phải trang Admin vui lòng truy cập https:://lssdhrtime.com/admin/");
        }


        if ($user->employee?->trashed()) {
            $deletedBy = $user->employee->delete_by ?? 'Admin';
            return redirect()->back()->with('error', "Tài khoản đã bị vô hiệu hóa bởi $deletedBy.");
        }

        Auth::login($user);
        // ✅ Thêm flash message
        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            ], 401);
        }
        if ($user->isDownAdminRole()) {

            // (tuỳ chọn) check role admin
            // if ($user->role !== 'admin') {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Bạn không phải ADMIN'
            //     ], 403);
            // }

            if ($user->employee?->trashed()) {
                $deletedBy = $user->employee->delete_by ?? 'Con Bò';
                return response()->json([
                    'status' => 'error',
                    'message' => "Tài khoản đã bị vô hiệu hóa bởi $deletedBy."
                ], 403);
            }

            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Đăng nhập thành công!',
                'redirect' => route('home')
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không phải ADMIN'
            ], 403);
        }
    }
    public function logout()
    {
        $user = Auth::user(); // lấy user hiện tại\

        Auth::logout();

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.login');
        }

        // return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
        return redirect()->route('login');
    }
}
