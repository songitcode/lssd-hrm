@extends('layouts.app')

@section('title', 'Nhân Sự')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    <div class="container group-employee">
        <div class="row group-function">
            <div class="col-md-6">
                <div class="search-wrapper position-relative">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                       <input type="text" id="search-employee" class="form-control search-input" placeholder="Tìm tên sĩ quan hoặc tên đăng nhập...">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Nút tạo nhân sự -->
                <div class="d-flex justify-content-end mb-2 gap-2">
                    <button class="btn-action-hr btn-history" data-bs-toggle="modal"
                        data-bs-target="#historyModal"><strong><i class="fa-solid fa-clock-rotate-left"></i> Lịch
                            Sử</strong></button>
                            
                     <button class="btn-action-hr btn-trash" data-bs-toggle="modal"
                        data-bs-target="#trashModal">
                        <strong><i class="fa-solid fa-trash-can"></i> Thùng Rác</strong>
                    </button>

                    <button class="btn-action-hr btn-add" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <strong>
                            <i class="fa fa-circle-plus"></i> 
                            Tạo Nhân Sự
                        </strong>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal hiển thị lịch sử LOGS -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyModalLabel">Lịch Sử Thao Tác</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Lịch sử mẫu -->
                        <!-- Xóa -->
                        @forelse ($logs as $log)
                         @switch($log->action)
                                @case('xóa')
                                    <div class="history-item d-flex justify-content-between align-items-center text-danger">
                                        <div>
                                            <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> 
                                            đã <span class="_Mau">xóa <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong></span>
                                            vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                        </div>
                                        @if(in_array($log->target, $deletedUsernames) && $latestDeleteLogByUser[$log->target] === $log->id)
                                            {{-- Nếu user bị xoá và đây là log xoá mới nhất → cho phép khôi phục --}}
                                            <form method="POST" action="{{ route('employees.restore', $log->target) }}" class="form-restore" >
                                                @csrf
                                                <button type="submit" class="btn btn-restore">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> Khôi phục
                                                </button>
                                            </form>
                                        @elseif(isset($employeeMap[$log->target]))
                                            {{-- Nếu user tồn tại trở lại sau khi xóa --}}
                                            <span class="badge bg-success">Đã hồi sinh</span>
                                        @else
                                            {{-- Không tồn tại (bị xoá nhưng không phải log mới nhất) --}}
                                            <span class="text-muted">(đã xoá)</span>
                                        @endif
                                    </div>
                                @break

                                @case('tạo')
                                    <div class="history-item text-info">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> <span class="_Mau">đã ban sự sống cho</span> <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i:s, d/m/Y') }}
                                    </div>
                                @break

                                @case('sửa')
                                    <div class="history-item text-primary">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> vừa <strong class="_">{{ $log->detail }}</strong> cho <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                    </div>
                                @break

                                @case('khôi phục')
                                    <div class="history-item text-success">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> đã cứu lấy linh hồn <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                    </div>
                                @break

                                @case('đổi mật khẩu')
                                    <div class="history-item text-warning">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> 
                                        vừa <strong class="_Mau">{{ $log->detail }}</strong> 
                                        cho <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                    </div>
                                @break

                                @case('resetPassword')
                                    <div class="history-item" style="color: #58A0C8;">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> 
                                        đã <strong class="_Mau">{{ $log->detail }}</strong> 
                                        của <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                    </div>
                                @break

                                @default
                                    <div class="history-item">
                                        <strong>{{ $employeeMap[$log->user->username] ?? $log->user->username }}</strong> {{ $log->detail }} với <strong>{{ $employeeMap[$log->target] ?? $log->target }}</strong>
                                        vào lúc {{ $log->created_at->format('H:i, d/m/Y') }}
                                    </div>
                            @endswitch
                        @empty
                            <div class="text-center"><i class="fa-solid fa-clock-rotate-left"></i> Không có lịch sử thao tác nào.</div>
                        @endforelse
                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-3">
                           {{--  {{ $logs->links() }} --}}
                            <span class="ms-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thùng Rác -->
        <div class="modal fade" id="trashModal" tabindex="-1" aria-labelledby="trashModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="trashModalLabel"><i class="fa-solid fa-trash-can"></i> Thùng Rác</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        @if ($deletedEmployees->isEmpty())
                            <p class="text-center text-muted">Không có nhân sự nào trong thùng rác.</p>
                        @else
                            <form method="POST" action="{{ route('employees.force-delete-multiple') }}"
                                onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn toàn bộ?');" class="form-delete-trash">
                                @csrf
                                @method('DELETE')
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>
                                                    <input type="checkbox" id="selectAllTrash" class="d-none"/>
                                                    <label for="selectAllTrash" class="btn-select-all">Nhấn chọn tất cả</label>
                                                </th>
                                                <th>Tên Ingame</th>
                                                <th>Username</th>
                                                <th>Chức Vụ</th>
                                                <th>Quân Hàm</th>
                                                <th>Ngày Xóa</th>
                                                <!-- <th>Hành Động</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deletedEmployees as $emp)
                                                <tr>
                                                    <td>
                                                        <input class="btn-check-trash-delete" type="checkbox" name="ids[]" value="{{ $emp->id }}" />
                                                    </td>
                                                    <td>{{ $emp->name_ingame }}</td>
                                                    <td>{{ $emp->user->username }}</td>
                                                    <td>{{ $emp->position->name_positions ?? '-' }}</td>
                                                    <td>{{ $emp->rank->name_ranks ?? '-' }}</td>
                                                    <td>{{ $emp->deleted_at->format('d/m/Y H:i:s') }}</td>
                                                    {{--  
                                                    <td>
                                                        <form method="POST" action="{{ route('employees.force-delete', $emp->id) }}"
                                                            style="display:inline;" onsubmit="return confirm('Xóa vĩnh viễn nhân sự này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger"><i class="fa-solid fa-xmark"></i> Xóa Vĩnh Viễn</button>
                                                        </form>
                                                    </td>
                                                    --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-danger" id="btnDeleteSelected">
                                        <i class="fa-solid fa-trash"></i> Xóa Vĩnh Viễn
                                    </button>
                                </div>
                            </form>
                        @endif
                        @if(session('warning'))
                            <div class="alert alert-warning mt-2">{{ session('warning') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tạo Nhân Sự-->
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-lg">

                    <!-- Header -->
                    <div class="modal-header bg-warning text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="createUserModalLabel">Tạo Nhân Sự Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body px-4 py-4">
                        <form class="form-create-employee" id="createUserForm" method="POST" action="{{ route('employees.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Cột trái: Ảnh đại diện -->
                                <div class="col-md-4 text-center">
                                    <img id="avatarPreview" src="{{ asset('assets/images/user_preview_logo.png') }}"
                                        class="img-thumbnail rounded-circle mb-3 shadow-sm"
                                        style="width: 220px; height: 220px; object-fit: cover;">
                                    <!-- Nút chọn ảnh tùy biến -->
                                    <input type="file" name="avatar" accept="image/*" class="d-none" id="avatarInput"
                                        onchange="previewAvatar(event)">
                                    <label for="avatarInput" class="btn btn-outline-primary btn-sm px-3 py-2 mt-2">
                                        <i class="bi bi-image"></i> Chọn Ảnh
                                    </label>

                                </div>

                                <!-- Cột phải: Thông tin -->
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="box-info">
                                                <label class="form-label">Tên Ingame</label>
                                                <input type="text" name="name_ingame"
                                                    class="form-control @error('name_ingame') is-invalid @enderror"
                                                    placeholder="Nhập tên ingame" required>
                                                @error('name_ingame')
                                                    <div class="invalid-feedback">Tên ingame có vẻ sai sai</div>
                                                @enderror
                                            </div>
                                            <div class="box-info">
                                                <label class="form-label">Tên đăng nhập</label>
                                                <input type="text" name="username"
                                                    class="form-control @error('username') is-invalid @enderror"
                                                    placeholder="Nhập username" required>
                                                @error('username')
                                                    <div class="invalid-feedback">Trùng với tên người khác</div>
                                                @enderror
                                            </div>

                                            <div class="box-info">
                                                <label class="form-label">Mật khẩu</label>
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Nhập mật khẩu" required>
                                                @error('password')
                                                    {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                                                    <div class="invalid-feedback">Mật khẩu thiếu thiếu gì rồi</div>
                                                @enderror
                                            </div>

                                            <div class="box-info">
                                                <label class="form-label">Xác nhận mật khẩu</label>
                                                <input type="password" name="password_confirmation"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    placeholder="Nhập lại mật khẩu" required>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback">Không khớp mật khẩu</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="box-info">
                                                <label class="form-label">Chức vụ</label>
                                                <select name="position_id" class="form-select">
                                                    @foreach($positions as $position)
                                                        <option value="{{ $position->id }}">{{ $position->name_positions }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="box-info">
                                                <label class="form-label">Quân hàm</label>
                                                <select name="rank_id" class="form-select">
                                                    @foreach($ranks as $rank)
                                                        <option value="{{ $rank->id }}">{{ $rank->name_ranks }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nút tạo / hủy -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-success me-2 px-4">Tạo Mới</button>
                                        <button type="button" class="btn btn-secondary px-4"
                                            data-bs-dismiss="modal">Hủy</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- Modal Sửa Nhân Sự -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold">Chỉnh sửa Nhân Sự</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <form class="form-edit-employee" id="editUserForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body px-4 py-4">
                            <input type="hidden" name="id" id="edit_id">

                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img id="edit_avatar_preview" src="" width="100%" class="img-thumbnail rounded-circle mb-3 shadow-sm"
                                        style="width: 220px; height: 220px; object-fit: cover;">
                                </div>
                                <div class="col-md-8 row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tên ingame</label>
                                        <input type="text" class="form-control" id="edit_name" name="name_ingame" required>

                                        <label class="form-label mt-2">Chức vụ</label>
                                        <select class="form-select" name="position_id" id="edit_position_id" required>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->name_positions }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" id="edit_username" disabled>

                                        <label class="form-label mt-2">Quân hàm</label>
                                        <select class="form-select" name="rank_id" id="edit_rank_id" required>
                                            @foreach($ranks as $rank)
                                                <option value="{{ $rank->id }}">{{ $rank->name_ranks }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                data-bs-dismiss="modal">
                                🔐 Đổi mật khẩu
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="editSubmitBtn" disabled>Cập nhật</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Đổi Mật Khẩu -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
            aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <button type="button" class="m-2 btn-resetpassword" onclick="resetPasswordFromModal()">
                    Quên Mật Khẩu Cũ
                </button>
                <form id="changePasswordForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="change_password_id">

                        <div class="mb-3">
                            <label for="old_password" class="form-label">Mật khẩu cũ</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                            <div class="text-danger small" id="error_old_password"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="text-danger small" id="error_new_password"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                             <div class="text-danger small" id="error_new_password_confirmation"></div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleAllPasswords">
                            <i class="fa-regular fa-eye"></i> Hiện mật khẩu
                        </button>
                        <div>
                            <button type="submit" class="btn btn-danger">Đổi mật khẩu</button>
                            <button type="submit" class="btn btn-primary" onclick="goBackToEditModal()">Quay Lại</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <div class="box-employees">
            <!-- Bảng nhân sự -->
            <div class="table-responsive employees-position-relative">
                <div id="loading-spinner" style="text-align: center; margin: 10px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <table class="table table-striped mb-0 table-employees table-radius-custom">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>AVT</th>
                            <th>Tên Sĩ Quan</th>
                            <th>Tên Đăng Nhập</th>
                            <th>Chức Vụ</th>
                            <th>Quân Hàm</th>
                            <th>Ngày Tạo</th>
                            <th>Người Tạo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paginated as $index => $emp)
                            <tr class="{{ in_array($emp->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'glow' : '' }}">
                                <td>{{ $loop->iteration + ($paginated->currentPage() - 1) * $paginated->perPage() }}</td>
                                    <td> @if ($emp->avatar)
                                            <img src="{{ asset('storage/' . $emp->avatar) }}" alt="Avatar" class="rounded-circle"
                                                width="30" height="30">
                                        @else
                                            <img src="{{ asset('assets/images/user_preview_logo.png') }}" alt="Default"
                                                class="rounded-circle" width="30" height="30">
                                        @endif
                                    </td>
                                <td>
                                    {{ $emp->name_ingame ?? '-' }}
                                </td>
                                <td><span>{{ $emp->user->username ?? '-' }}</span>
                                </td>
                                <td>{{ $emp->position->name_positions ?? '-' }}</td>
                                <td>{{ $emp->rank->name_ranks ?? '-' }}</td>
                                <td>{{ $emp->created_at->format('d/m/Y') }}</td>
                                <td>{{ $emp->userCreatedBy->username ?? 'Admin' }}</td>
                                <td>
                                    @if(auth()->id() !== $emp->user_id)
                                        <form method="POST" class="form-delete-employee"
                                            action="{{ route('employees.destroy', ['id' => Hashids::encode($emp->id)]) }}"
                                            style="display: inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa nhân sự này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete">
                                                <i class="fa-solid fa-trash"></i> Xóa
                                            </button>
                                        </form>

                                        <!-- <button class="btn btn-edit"><i class="fa-solid fa-user-pen"></i> Sửa</button> -->
                                         <button class="btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $emp->id }}"
                                            data-name="{{ $emp->name_ingame }}"
                                            data-position="{{ $emp->position_id }}"
                                            data-rank="{{ $emp->rank_id }}"
                                            data-username="{{ $emp->user->username }}"
                                            data-avatar="{{ asset('storage/' . $emp->avatar) }}">
                                            <i class="fa-solid fa-user-pen"></i> Sửa
                                        </button>
                                    @else
                                        <button class="btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $emp->id }}"
                                            data-name="{{ $emp->name_ingame }}"
                                            data-position="{{ $emp->position_id }}"
                                            data-rank="{{ $emp->rank_id }}"
                                            data-username="{{ $emp->user->username }}"
                                            data-avatar="{{ asset('storage/' . $emp->avatar) }}">
                                            <i class="fa-solid fa-user-pen"></i> Đổi Mật Khẩu
                                        </button>
                                    @endif
                                     <!-- <button class="btn btn-edit"><i class="fa-solid fa-user-pen"></i> Sửa</button> -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">Không có nhân sự nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-3">
                {{ $paginated->links() }}
            </div>

        </div>
    </div>
@endsection 
@push('scripts')
<script src="{{ asset('assets/js/employee_index.js') }}"></script>
<script>
        document.querySelector('.form-edit-employee').addEventListener('submit', function (e) {
            showLoading();
        });
        document.querySelector('.form-create-employee').addEventListener('submit', function (e) {
            showLoading();
        });
        document.querySelector('.form-restore').addEventListener('submit', function (e) {
            showLoading();
        });
        document.querySelector('.form-delete-trash').addEventListener('submit', function (e) {
            showLoading();
        });
        document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete-employee');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function () {
                showLoading();
            });
        });
    });
</script>
@endpush
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('createUserModal'));
            modal.show();
        });
    </script>
@endif
