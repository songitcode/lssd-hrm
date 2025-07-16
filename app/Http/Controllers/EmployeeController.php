<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Rank;
use App\Models\ActivityLog;
use Vinkla\Hashids\Facades\Hashids;
class EmployeeController extends Controller
{

    // Hiển Thị
    public function index()
    {
        $employees = Employee::sortedByCustomPosition(); // chỉ lấy nhân sự hoạt động
        $deletedEmployees = Employee::onlyTrashed()->with(['user', 'position', 'rank'])->get(); // lấy nhân sự trong "thùng rác"

        // Phân trang thủ công
        $perPage = 10;
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
        $logs = ActivityLog::with('user')->latest()->take(50)->get();
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

        // ✅ Phân trang thủ công sau khi sort
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $pagedData = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedUsers = new LengthAwarePaginator(
            $pagedData,
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('home', ['users' => $paginatedUsers]);
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

    // Xóa, Remove, Delete, Xóa mề, soft delete
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
            // abort(403, 'Bạn không đủ quyền để xóa người này.');
            return redirect()->back()->with('warning', 'Bạn không đủ thẩm quyền xóa người.');
        }

        $username = $targetUser->username ?? 'Không rõ';

        $employee->delete();

        ActivityLog::create([
            'user_id' => $currentUser->id,
            'action' => 'xóa',
            'target' => $username,
            'detail' => 'cưới lấy sự sống'
        ]);

        return redirect()->back()->with('success', 'Đã chuyển nhân sự vào thùng rác.');
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
        // ActivityLog::where('action', 'xóa')
        //     ->where('target', $employee->user->username)
        //     ->delete();
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

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sửa',
            'target' => $employee->user->username,
            'detail' => 'đã cập nhật thông tin'
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

    //// RESET PASSWORD
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
            'detail' => 'đã xóa vĩnh viễn khỏi hệ thống'
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
                'detail' => 'xóa vĩnh viễn'
            ]);

            $emp->forceDelete();
            $user?->delete();
        }

        return back()->with('success', 'Đã xóa vĩnh viễn các nhân sự đã chọn.');
    }


    // Tìm kiếm

    //// PROFILE SETTING
    public function profile()
    {
        $employee = auth()->user()->employee;

        $positions = Position::all();
        $ranks = Rank::all();

        $currentRoleLevel = auth()->user()->getRoleLevel();
        $targetRoleLevel = $employee?->user?->getRoleLevel() ?? 0;

        // Chỉ được chỉnh nếu user hiện tại có cấp bậc cao hơn nhân sự đó
        $canEditPosition = $currentRoleLevel > $targetRoleLevel;

        return view('profile', compact('employee', 'positions', 'ranks', 'canEditPosition'));
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

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'sửa',
            'target' => $user->username,
            'detail' => 'đã cập nhật hồ sơ cá nhân'
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    ////
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
}


