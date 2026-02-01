<?php
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckManagerRole;
use App\Http\Controllers\{DiscordController, HomeController, EmployeeController, ActivityLogController, AttendanceController, SalaryConfigController, PayrollController, OnDutyController, OfficeMemberController};
use App\Models\Attendance;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

// Route::get('/fix-storage-force', function () {
//     $targetFolder = storage_path('app/public');
//     $linkFolder = public_path('storage');

//     if (file_exists($linkFolder)) {
//         // Remove real folder or broken symlink
//         if (is_link($linkFolder) || is_dir($linkFolder)) {
//             File::deleteDirectory($linkFolder); // Works for symlinks and folders
//         } else {
//             unlink($linkFolder); // Fallback
//         }
//     }

//     File::link($targetFolder, $linkFolder);
//     return 'Symlink recreated successfully!';
// });

Route::get('/', function () {return view('auth.login');});
// Hiển thị trang admin login
Route::get('/admin', function () {
    return view('auth.admin.login');
})->name('admin.login');

Route::get('/employees', function () {
    return view('employees.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    // Route::get('/', function () { return view('maintenance'); });
    Route::post('/', [LoginController::class, 'login']);
    // Admin Login Routes
    Route::post('/admin/login', [LoginController::class, 'loginAdmin'])
        ->name('admin.login.submit');
});

/// Liên Kết Discord
Route::get('/discord/connect', [DiscordController::class, 'connect'])->name('discord.connect');
Route::get('/discord/callback', [DiscordController::class, 'callback'])->name('discord.callback');
Route::post('/discord/unlink', [DiscordController::class, 'unlink'])
    ->name('discord.unlink')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    Route::get('/home', [EmployeeController::class, 'homeDisplay'])->name('home');

    // PROFILE
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [EmployeeController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/avatar', [EmployeeController::class, 'deleteAvatar'])->name('profile.deleteAvatar');

    // LOGS
    Route::delete('/activity-log/{id}', [EmployeeController::class, 'deleteLog'])->name('logs.delete');
    Route::delete('/activity-logs/clear', [EmployeeController::class, 'deleteLogsActive'])->name('logs.clear');

    // TRASH
    Route::delete('/employees/trash/delete/{id}', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
    Route::delete('/employees/trash/delete-all', [EmployeeController::class, 'forceDeleteMultiple'])->name('employees.force-delete-multiple');

    // CHẤM CÔNG, ONDUTY
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check', [AttendanceController::class, 'check'])->name('attendance.check');
    Route::put('/attendance/{id}/force-checkout', [AttendanceController::class, 'forceCheckout'])
        ->name('attendance.force_checkout')
        ->middleware('auth');
    // routes fetch trạng thái chấm công
    Route::get('/attendance/status', [AttendanceController::class, 'status'])->name('attendance.status');
    Route::delete('/attendance/{id}/huy-onduty', [AttendanceController::class, 'huyCheckin'])->name('attendance.huyCheckin');

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
    // FORM XIN NGHỈ PHÉP
    Route::get('/don-xin-nghi-phep', [HomeController::class, 'viewTakeLeave'])->name('partials.take_leave');

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
    Route::get('/payroll/search', [PayrollController::class, 'search'])->name('payroll.search');

    Route::get('/salary-configs', [SalaryConfigController::class, 'index'])->name('salary_configs.index');
    Route::post('/salary-configs', [SalaryConfigController::class, 'store'])->name('salary_configs.store');
    Route::put('/salary_configs/{id}', [SalaryConfigController::class, 'update'])->name('salary_configs.update');

    Route::put('/salary-configs/global-hours', [SalaryConfigController::class, 'updateGlobalHours'])->name('salary_configs.updateGlobalHours');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/user/{user}', [PayrollController::class, 'showUserAttendance'])->name('payroll.user_attendance');
    Route::get('/payroll/previous', [PayrollController::class, 'previousMonthPayroll'])->name('payroll.previous');
    Route::delete('/payroll/previous', [PayrollController::class, 'deletePreviousMonth'])->name('payroll.previous.delete');
    Route::get('/payroll/months', [PayrollController::class, 'getAvailableMonths']);
    Route::get('/payroll/summary', [PayrollController::class, 'summary']);
    Route::delete('/payroll/summary', [PayrollController::class, 'deleteMonth']);
    Route::delete('/payroll/user/{id}', [PayrollController::class, 'deleteAttendance'])->name('attendance.destroy');
    Route::post('/payroll/user/{id}', [PayrollController::class, 'updateInline'])
        ->name('attendance.updateInline');

    Route::get('/onduty', [OnDutyController::class, 'index'])->name('partials.ondutyList');

    // Reset toàn bộ dữ liệu chấm công WARNING!!
    Route::delete('/payroll/reset', [AttendanceController::class, 'resetAttendanceDta'])->name('attendance.resetAttendanceDta');

    Route::post('/employees/{id}/reset-password', [EmployeeController::class, 'resetPassword']);
});


