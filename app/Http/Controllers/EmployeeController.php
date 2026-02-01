<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\Models\{User, Employee, Position, Rank, ActivityLog, Attendance, WorkHourConfig};

class EmployeeController extends Controller
{
    public function deleteLogsActive()
    {
        $logs = ActivityLog::first(); // Lấy bản ghi đầu (trả về null nếu không có dữ liệu)
        if ($logs === null) {
            return redirect()->back()->with('error', 'Bảng ghi trống');
        }
        ActivityLog::truncate(); // Xóa toàn bộ dữ liệu + reset ID
        return redirect()->back()->with('success', 'Đã xóa toàn bộ activity logs');
    }
    // Hiển Thị
    public function index()
    {
        $employees = Employee::sortedByCustomPosition(); // chỉ lấy nhân sự hoạt động
        $deletedEmployees = Employee::onlyTrashed()->with(['user', 'position', 'rank'])->get(); // lấy nhân sự trong "thùng rác"

        // Phân trang thủ công
        $perPage = 20;
        $page = request()->get('page', 1);
        $paginated = new LengthAwarePaginator(
            $employees->forPage($page, $perPage),
            $employees->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $positions = Position::all();
        $ranks = Rank::all();
        $logs = ActivityLog::with('user')->latest()->take(200)->get();
        // $deletedEmployees = Employee::onlyTrashed()->with('user')->get();
        $deletedUsernames = $deletedEmployees->pluck('user.username')->toArray();

        // map username → name_ingame
        $employeeMap = Employee::withTrashed()->with('user')->get()->mapWithKeys(function ($emp) {
            return [$emp->user->username => $emp->name_ingame];
        });

        $latestDeleteLogByUser = $logs
            ->where('action', 'xóa')
            ->groupBy('target')
            ->map(function ($group) {
                return $group->first()->id; // ID log xóa mới nhất theo target
            });

        return view('employees.index', compact(
            'employees', // danh sách gốc
            'positions',
            'ranks',
            'logs',
            'deletedEmployees',
            'deletedUsernames',
            'employeeMap',
            'paginated',
            'latestDeleteLogByUser', // áp dụng cho employees, không get() trước paginate
        ));
    }

    public function homeDisplay()
    {
        $positionOrder = [
            'Cục Trưởng' => 1,
            'Phó Cục Trưởng' => 2,
            'Trợ Lý Cục Trưởng' => 3,
            'Thư Ký' => 4,
            'Đội Trưởng' => 5,
            'Đội Phó' => 6,
            'Cảnh Sát Viên' => 7,
            'Sĩ Quan Dự Bị' => 8,
            'Thực Tập' => 9,
        ];

        $users = User::with(['employee.position.rank'])
            ->where('role', '!=', 'admin')
            ->whereHas('employee', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get()
            ->sort(function ($a, $b) use ($positionOrder) {
                $aPriority = $positionOrder[$a->employee->position->name_positions] ?? 999;
                $bPriority = $positionOrder[$b->employee->position->name_positions] ?? 999;

                return $aPriority <=> $bPriority;
            });

        $tongSoNhanVien = User::where('role', '!=', 'admin')
            ->whereHas('employee', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->count();
        $capBacCao = ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng', 'Thư Ký', 'Đội Trưởng'];
        $soNhanVienCapCao = User::where('role', '!=', 'admin')
            ->whereHas('employee', function ($query) use ($capBacCao) {
                $query->whereNull('deleted_at')
                    ->whereHas('position', function ($q) use ($capBacCao) {
                        $q->whereIn('name_positions', $capBacCao);
                    });
            })
            ->count();

        // ✅ Phân trang thủ công sau khi sort
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 7;
        $pagedData = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedUsers = new LengthAwarePaginator(
            $pagedData,
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Logic hiển thị top 10 onduty
        $ondutyRanking = Attendance::with('user.employee.rank.salaryConfig')
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $rows) {

                $totalSessions = $rows->count();
                $totalHours = round($rows->sum('duration'), 2);
                $totalWage = $rows->sum('wage');

                $errorCount = 0;
                $forcedClosedCount = 0;

                foreach ($rows as $r) {

                    // Bỏ qua ca chưa checkout
                    if (!$r->check_in || !$r->check_out) {
                        // $errorCount++;
                        continue;
                    }

                    $realHours = round(
                        Carbon::parse($r->check_in)
                            ->diffInSeconds(Carbon::parse($r->check_out)) / 3600,
                        2
                    );

                    $maxHour = $r->user?->employee?->rank?->salaryConfig?->max_hours_per_day
                        ?? WorkHourConfig::currentMaxHour();

                    // LỖI NẶNG: vượt quá max 1h
                    if ($realHours > $maxHour + 1) {
                        $errorCount++;
                        continue;
                    }

                    // Bị quản lý tắt
                    $status = trim((string) $r->status);
                    if (
                        str_contains($status, 'Quản Lý') &&
                        !str_contains($status, 'Dư')
                    ) {
                        $forcedClosedCount++;
                    }
                }

                $completedCount = max(
                    0,
                    $totalSessions - ($errorCount + $forcedClosedCount)
                );

                $completionRate = $totalSessions > 0
                    ? round(($completedCount / $totalSessions) * 100)
                    : 0;

                return [
                    'user' => $rows->first()->user,
                    'onduty_count' => $totalSessions,
                    'total_hours' => $totalHours,
                    'errors' => $errorCount,
                    'forced_closed' => $forcedClosedCount,
                    'completion_rate' => $completionRate,
                    'total_wage' => $totalWage,
                ];
            })
            ->sortByDesc('total_hours')
            ->take(10)
            ->values();

        return view(
            'home',
            [
                'users' => $paginatedUsers,
                'ondutyRanking' => $ondutyRanking
            ],
            compact('tongSoNhanVien', 'soNhanVienCapCao')
        );
    }

    // TẠO, THÊM, ADD
    public function store(Request $request)
    {
        $request->validate([
            'name_ingame' => 'required|string|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed',
            'position_id' => 'required|exists:positions,id',
            'rank_id' => 'required|exists:ranks,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB
        ]);

        // Kiểm tra role tạo (chỉ quyền cao mới được)
        if (!in_array(auth()->user()->role, ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'])) {
            abort(403, 'Bạn không có quyền tạo nhân sự');
        }

        // Tạo user
        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $this->mapPositionToRole($request->position_id)
        ]);

        // Xử lý avatar nếu có
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Tạo nhân sự
        Employee::create([
            'user_id' => $user->id,
            'name_ingame' => $request->name_ingame,
            'position_id' => $request->position_id,
            'rank_id' => $request->rank_id,
            'avatar' => $avatarPath,
            'created_by' => auth()->id()
        ]);
        // dd($request->all());

        // Ghi log thao tác
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'tạo',
            'target' => $request->username,
            'detail' => 'ban phước lành cho'
        ]);

        return redirect()->back()->with('success', 'Tạo nhân sự thành công');
    }

    // Xóa, Remove, Delete, Xóa mềm, soft delete
    public function destroy($id)
    {
        $decoded = Hashids::decode($id);
        if (empty($decoded)) {
            abort(404);
        }

        $employee = Employee::with('user')->findOrFail($decoded[0]);
        $currentUser = auth()->user();
        $targetUser = $employee->user;

        if ($this->getRoleLevel($currentUser->role) <= $this->getRoleLevel($targetUser->role)) {
            $msg = 'Bạn không đủ thẩm quyền xóa người.';
            return request()->expectsJson()
                ? response()->json(['message' => $msg], 403)
                : redirect()->back()->with('warning', $msg);
        }

        $username = $targetUser->username ?? 'Không rõ';
        $employee->delete();

        ActivityLog::create([
            'user_id' => $currentUser->id,
            'action' => 'xóa',
            'target' => $username,
            'detail' => 'bỏ vào thùng rác'
        ]);

        $msg = 'Đã chuyển nhân sự vào thùng rác.';
        return request()->expectsJson()
            ? response()->json(['message' => $msg])
            : redirect()->back()->with('success', $msg);
    }

    // Phục hồi, Khôi phục
    public function restore($username)
    {
        if (!in_array(auth()->user()->role, ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'])) {
            abort(403, 'Bạn không có quyền khôi phục');
        }

        // Tìm user theo username
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withErrors('Không tìm thấy user.');
        }

        // Tìm nhân sự đã bị soft delete
        $employee = Employee::withTrashed()->where('user_id', $user->id)->first();

        if (!$employee || !$employee->trashed()) {
            return redirect()->back()->withErrors('Nhân sự không tồn tại hoặc chưa bị xóa.');
        }

        $employee->restore();

        // Ghi log khôi phục
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'khôi phục',
            'target' => $username,
            'detail' => 'đã hồi sinh '
        ]);
        // Xóa bỏ các log xóa trước đó của bản ghi này (nếu muốn)
        ActivityLog::where('action', 'xóa')
            ->where('target', $employee->user->username)
            ->delete();
        return redirect()->back()->with('success', 'Đã khôi phục nhân sự thành công.');
    }

    // Sửa, update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ingame' => 'required|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'rank_id' => 'required|exists:ranks,id',
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employee = Employee::with('user')->findOrFail($id);

        if ($this->getRoleLevel(auth()->user()->role) <= $this->getRoleLevel($employee->user->role)) {
            // abort(403, 'Bạn không đủ quyền chỉnh sửa người này.');
            return redirect()->back()->with('warning', 'Bạn không đủ quyền chỉnh sửa người này.');
        }

        $data = [
            'name_ingame' => $request->name_ingame,
            'position_id' => $request->position_id,
            'rank_id' => $request->rank_id,
        ];

        // Xử lý avatar mới nếu có
        // if ($request->hasFile('avatar')) {
        //     // Xoá ảnh cũ nếu có
        //     if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
        //         Storage::disk('public')->delete($employee->avatar);
        //     }

        //     $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        // }

        $newName = $request->input('name_ingame');
        $newPositionId = $request->input('position_id');
        $newRankId = $request->input('rank_id');
        // So sánh với dữ liệu cũ
        $hasChanged = (
            $newName !== $employee->name_ingame ||
            $newPositionId != $employee->position_id ||
            $newRankId != $employee->rank_id
        );

        if (!$hasChanged) {
            return redirect()->back()->with('warning', 'Bạn chưa thay đổi thông tin người này!');
        }

        $employee->update($data);

        // Đồng bộ role của User dựa theo chức vụ mới
        $newRole = $this->mapPositionToRole($newPositionId);
        $employee->user->update([
            'role' => $newRole,
        ]);

        // Ghi log chi tiết
        $detail = $newPositionId == $hasChanged ? 'cập nhật Quân hàm - Chức vụ' : 'cập nhật thông tin';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sửa',
            'target' => $employee->user->username,
            'detail' => $detail
        ]);

        return redirect()->back()->with('success', 'Cập nhật thành công!');
    }

    // Thay đổi mật khẩu
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $employee = Employee::with('user')->findOrFail($id);
        $user = $employee->user;

        // Kiểm tra quyền sửa
        if (auth()->id() !== $user->id && $this->getRoleLevel(auth()->user()->role) <= $this->getRoleLevel($user->role)) {
            abort(403, 'Bạn không đủ quyền đổi mật khẩu người này.');
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'đổi mật khẩu',
            'target' => $user->username,
            'detail' => 'đã cập nhật mật khẩu mới'
        ]);

        // return back()->with('success', 'Đổi mật khẩu thành công.');
        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }

    // RESET PASSWORD
    public function resetPassword($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $user = $employee->user;

        // Chỉ chính chủ hoặc admin có quyền
        if (auth()->id() !== $user->id && $this->getRoleLevel(auth()->user()->role) <= $this->getRoleLevel($user->role)) {
            return response()->json(['message' => 'Bạn không đủ quyền reset mật khẩu người này.'], 403);
        }

        $user->update([
            'password' => bcrypt('123456789'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'resetPassword',
            'target' => $user->username,
            'detail' => 'cứu lấy mật khẩu'
        ]);

        return response()->json(['message' => 'Mật khẩu đã được đặt lại thành 123456789.']);
    }

    // Xóa 1 nhân sự trong thùng rác, Hard delete
    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $user = User::find($employee->user_id);

        // if (!in_array(auth()->user()->role, ['admin', 'cục trưởng'])) {
        //     abort(403, 'Bạn không có quyền xóa vĩnh viễn.');
        // }

        // Xóa avatar nếu tồn tại
        if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->forceDelete();
        $user?->delete(); // xóa tài khoản nếu cần

        if (empty($ids)) {
            return back()->with('warning', 'Bạn chưa chọn nhân sự nào để xóa.');
        }

        // Tạo active logs
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'xóa vĩnh viễn',
            'target' => $user?->username ?? 'Ẩn danh',
            'detail' => 'xóa vĩnh viễn khỏi hệ thống'
        ]);

        return back()->with('success', 'Đã xóa vĩnh viễn nhân sự.');
    }

    // Xóa n nhân sự trong thùng rác, Hard delete
    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('warning', 'Bạn chưa chọn nhân sự nào để xóa.');
        }

        $employees = Employee::onlyTrashed()->whereIn('id', $ids)->get();

        foreach ($employees as $emp) {
            $user = User::find($emp->user_id);

            // Xóa avatar nếu có
            if ($emp->avatar && Storage::disk('public')->exists($emp->avatar)) {
                Storage::disk('public')->delete($emp->avatar);
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'xóa vĩnh viễn',
                'target' => $user?->username ?? 'Ẩn danh',
                'detail' => 'đổ rác  tái chế'
            ]);

            $emp->forceDelete();
            $user?->delete();
        }

        return back()->with('success', 'Đã xóa vĩnh viễn các nhân sự đã chọn.');
    }

    // PROFILE SETTING
    public function profile()
    {
        $employee = Auth::user()->employee;
        $positions = Position::all();
        $ranks = Rank::all();

        $currentRoleLevel = auth()->user()->getRoleLevel();
        $targetRoleLevel = $employee?->user?->getRoleLevel() ?? 0;
        // Chỉ được chỉnh nếu user hiện tại có cấp bậc cao hơn nhân sự đó
        $canEditPosition = $currentRoleLevel > $targetRoleLevel;
        $tongTienSuNghiep = auth()->user()->monthly_attendance_summaries->flatten()->sum('total_wage');

        return view('profile', compact('employee', 'positions', 'ranks', 'canEditPosition', 'tongTienSuNghiep'));
    }

    // UPDATE PROFILE, SỬA, THAY ĐỔI PROFILE
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        $request->validate([
            'name_ingame' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'position_id' => 'nullable|exists:positions,id',
            'rank_id' => 'nullable|exists:ranks,id',
        ]);

        // ✅ Cập nhật ảnh đại diện và tên
        $employee->name_ingame = $request->name_ingame;

        if ($request->hasFile('avatar')) {
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $employee->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->position_id && auth()->user()->getRoleLevel() > $employee->user->getRoleLevel()) {
            $employee->position_id = $request->position_id;
        }

        // Quân hàm thì vẫn cho phép chỉnh nếu có quyền
        if (auth()->user()->getRoleLevel() >= 1) {
            $employee->rank_id = $request->rank_id;
        }

        // 🔐 CHỈ cập nhật chức vụ nếu người dùng có role cao hơn nhân sự đó
        if (
            $request->filled('position_id') &&
            $employee->position_id != $request->position_id // chỉ đổi khi khác
        ) {
            $targetRole = $this->mapPositionToRole($employee->position_id);
            $newRole = $this->mapPositionToRole($request->position_id);

            $editorRole = $user->role;

            if (
                $this->getRoleLevel($editorRole) > $this->getRoleLevel($targetRole)
            ) {
                $employee->position_id = $request->position_id;

                // Đồng bộ luôn role của user nếu chức vụ đổi
                $employee->user->update([
                    'role' => $newRole
                ]);
            }
        }

        $employee->save();

        if ($request->hasFile('avatar')) {
            $detail = 'thay đổi ảnh đại diện';
        } else {
            $detail = 'cập nhật thông tin cá nhân';
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'sửa',
            'target' => $user->username,
            'detail' => $detail
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    // REMOVE PROFILE, XÓA
    public function deleteAvatar()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if ($employee && $employee->avatar) {
            if (Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $employee->avatar = null;
            $employee->save();
        }
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'deleteAvatar',
            'target' => $user->username,
            'detail' => 'xóa ảnh đại diện'
        ]);

        return redirect()->back()->with('success', 'Đã xoá ảnh đại diện.');
    }

    // Map chức vụ sang role
    private function mapPositionToRole($positionId)
    {
        $map = [
            'Thư Ký' => 'thư ký',
            'Trợ Lý Cục Trưởng' => 'trợ lý cục trưởng',
            'Phó Cục Trưởng' => 'phó cục trưởng',
            'Cục Trưởng' => 'cục trưởng'
        ];

        $position = Position::find($positionId);
        return $map[$position->name_positions] ?? 'thành viên';
    }
    private function getRoleLevel($role)
    {
        return match ($role) {
            'admin' => 5,
            'cục trưởng' => 4,
            'phó cục trưởng' => 3,
            'trợ lý cục trưởng' => 2,
            'thư ký' => 1,
            default => 0,
        };
    }
    // 

    // SEARCH, TÌM KIẾM NHÂN SỰ FETCH
    public function search(Request $request)
    {
        $query = $request->get('query');

        $employees = Employee::with(['user', 'position', 'rank', 'userCreatedBy'])
            ->where(function ($q) use ($query) {
                $q->where('name_ingame', 'like', "%{$query}%")
                    ->orWhereHas(
                        'user',
                        fn($u) =>
                        $u->where('username', 'like', "%{$query}%")
                    );

                if (is_numeric($query)) {
                    $q->orWhere('id', (int) $query);
                }
            })
            ->get();

        // return response()->json([
        //     'data' => $employees,
        // ]);
        return response()->json([
            'data' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'hash_id' => Hashids::encode($emp->id), // thêm dòng này
                    'name_ingame' => $emp->name_ingame,
                    'username' => $emp->user->username ?? '-',
                    'name_positions' => $emp->position->name_positions ?? '-',
                    'name_ranks' => $emp->rank->name_ranks ?? '-',
                    'rank_id' => $emp->rank->id ?? '-',
                    'position_id' => $emp->position->id ?? '-',
                    'created_at' => $emp->created_at,
                    'user_created_by' => $emp->userCreatedBy->username ?? 'Admin',
                    'avatar' => $emp->discord_avatar
                        ? $emp->discord_avatar
                        : ($emp->avatar
                            ? asset('storage/' . $emp->avatar)
                            : asset('/assets/images/user_preview_logo.png')),
                ];
            })
        ]);
    }
}