<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;
use App\Models\Attendance;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryConfigController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/employees', function () {
    return view('employees.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/restore/{username}', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/change-password/{id}', [EmployeeController::class, 'changePassword'])->name('employees.change-password');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');

    // PROFILE
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [EmployeeController::class, 'updateProfile'])->name('profile.update');

    // LOGS
    Route::delete('/activity-log/{id}', [EmployeeController::class, 'deleteLog'])->name('logs.delete');
    Route::delete('/activity-logs/clear', [EmployeeController::class, 'deleteAllLogs'])->name('logs.clear');

    // TRASH
    Route::delete('/employees/trash/delete/{id}', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
    Route::delete('/employees/trash/delete-all', [EmployeeController::class, 'forceDeleteMultiple'])->name('employees.force-delete-multiple');

    // CHẤM CÔNG, ONDUTY

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

