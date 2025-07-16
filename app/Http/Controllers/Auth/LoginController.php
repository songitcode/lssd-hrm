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
                    'Trợ Lý Cục Trưởng'
                ]);
            })
            ->get();
            
        return view('auth.login', compact('contacts'));
        // return view('auth.login');

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

        Auth::login($user);
        // ✅ Thêm flash message
        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    public function logout()
    {
        Auth::logout();
        // return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
        return redirect()->route('login');
    }
}
