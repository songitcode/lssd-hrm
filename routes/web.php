<?php
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckManagerRole;
use App\Http\Controllers\{HomeController, EmployeeController, ActivityLogController, AttendanceController, SalaryConfigController, PayrollController, OnDutyController};

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/home', function () {
//     return view('home');
// });

Route::get('/employees', function () {
    return view('employees.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {

    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    Route::get('/home', [EmployeeController::class, 'homeDisplay'])->name('home');

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
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check', [AttendanceController::class, 'check'])->name('attendance.check');

    // XÓA LỊCH SỬ CHẤM CÔNG
    Route::delete('/attendance/delete-month/{month}/{year}/{user}', [AttendanceController::class, 'deleteMonthlyHistory'])
        ->name('attendance.delete-month')
        ->middleware('can:manage-attendance'); // quyền cao mới được xóa

    // BẢO LÃNH TỘI PHẠM VIEW
    Route::get('/bao-lanh-toi-pham', [HomeController::class, 'viewCriminalBail'])->name('partials.criminal_bail');
    // FORM HỒ SƠ ĐÃ XỬ LÝ
    Route::get('/ho-tro-xu-an', [HomeController::class, 'viewProcRecords'])->name('partials.proc_records');
    // FORM HỒ SƠ HỖ TRỢ TRUY NÃ
    Route::get('/ho-tro-truy-na', [HomeController::class, 'viewWantedSupport'])->name('partials.wanted_support');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Kiểm tra role mới có quyền sử dụng tính năng quản lý
Route::middleware(['auth', CheckManagerRole::class])->group(function () {
    // EMPLOYEES, NHÂN VIÊN
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/restore/{username}', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/change-password/{id}', [EmployeeController::class, 'changePassword'])->name('employees.change-password');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');

    Route::get('/salary-configs', [SalaryConfigController::class, 'index'])->name('salary_configs.index');
    Route::post('/salary-configs', [SalaryConfigController::class, 'store'])->name('salary_configs.store');
    // Route::put('/salary-configs/update-hourly-rate/{id}', [SalaryConfigController::class, 'updateHourlyRate'])->name('salary_configs.update_hourly_rate');
    // Route::put('/salary_configs/{id}/update-max-hours', [SalaryConfigController::class, 'updateMaxHoursPerDay'])
    //     ->name('salary_configs.update_max_hours');
    Route::put('/salary_configs/{id}', [SalaryConfigController::class, 'update'])->name('salary_configs.update');

    Route::put('/salary-configs/global-hours', [SalaryConfigController::class, 'updateGlobalHours'])->name('salary_configs.updateGlobalHours');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/user/{user}', [PayrollController::class, 'showUserAttendance'])->name('payroll.user_attendance');

    Route::get('/onduty', [OnDutyController::class, 'index'])->name('partials.ondutyList');

    // Reset toàn bộ dữ liệu chấm công WARNING!!
    Route::delete('/payroll/reset', [AttendanceController::class, 'resetAll'])->name('attendance.resetAll');

});

