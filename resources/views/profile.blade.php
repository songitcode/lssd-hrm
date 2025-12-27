@extends('layouts.app')

@section('title', auth()->user()->employee->name_ingame ?? 'ADMIN')

@section('title', 'Hồ Sơ Cá Nhân')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush
@php
    $highRoles = ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'];
    $isHighRole = in_array(auth()->user()->role, $highRoles);
@endphp

{{--
@php
$currentUser = auth()->user();
$canEditPosition = $currentUser->canEditPositionOf($employee->user);
@endphp
--}}

@section('content')
    <div class="container py-5 profile-card">
        <form id="deleteAvatarForm" action="{{ route('profile.deleteAvatar') }}" method="POST" class="delete-avatar d-none">
            @csrf
            @method('DELETE')
        </form>

        <form class="box-profile p-4 loader" method="POST" action="{{ route('profile.update') }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row p-2 profile-card">
                @if ($employee)
                    <div class="col-md-4 text-center mt-5 align-items-cente">
                        <div class="avatar-circle mt-5">
                            <div class="profile-avatar-wrapper">
                                @if ($employee->discord_id ?? false)
                                    <img id="avatarPreview" src="{{ $employee->discord_avatar }}" alt="Avatar"
                                        class="profile-avatar mb-3"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'Admin') }}&background=random';">
                                @elseif ($employee && $employee->avatar)
                                    <img id="avatarPreview" src="{{ asset('storage/' . $employee->avatar) }}" alt="Avatar"
                                        class="profile-avatar mb-3"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'Admin') }}&background=random';">
                                @else
                                    <img id="avatarPreview"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'Admin') }}&background=random"
                                        class="profile-avatar mb-3" alt="{{ $employee->name_ingame }}">
                                @endif
                            </div>
                        </div>
                        @if (!$employee->discord_id ?? false)
                            <input type="file" name="avatar" accept="image/*" class="d-none" id="avatarInput"
                                onchange="previewAvatar(event)">
                            <label for="avatarInput" class="btn-change-img mt-4">
                                <i class="fa fa-image"></i> Chọn ảnh đại diện
                            </label>
                        @else
                            <p class="btn-change-img mt-4">{{ $employee->discord_username }}</p>
                        @endif
                        @if ($employee->avatar)
                            @if ($employee->discord_id ?? false)
                                <button type="button" onclick="confirmDeleteAvatar()" class="btn-remove-avt">Xoá ảnh đại diện trang web
                                    (Không xóa discord)</button>
                            @else
                                <br>
                                <button type="button" onclick="confirmDeleteAvatar()" class="btn-remove-avt">Xoá ảnh đại diện</button>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="mb-4"> Hồ Sơ Sĩ Quan <strong
                                        class="text-warning">{{ $employee->name_ingame }}</strong></h3>

                                <div class="mb-3">
                                    <label class="form-label"><b>Tên đăng nhập</b></label>
                                    <input type="text" class="form-control info-box input__view cursor_not_allowed"
                                        value="{{ $employee->user->username }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><b>Tên trong game GTA5VN</b></label>
                                    <br>
                                    @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'trợ lý cục trưởng']))
                                        <input type="text" name="name_ingame" class="info-box input__view cursor_not_allowed"
                                            value="{{ $employee->name_ingame }}" readonly required>
                                        {{--<p>{{ $employee->name_ingame }}</p>--}}
                                    @else
                                        <input type="text" name="name_ingame" class="info-box form-control input__view"
                                            value="{{ $employee->name_ingame }}" data-original="{{ $employee->name_ingame }}"
                                            required>
                                    @endif
                                </div>
                                <label class="form-label"><b>Chức vụ</b></label>
                                @if($canEditPosition)
                                    <div class="mb-3">
                                        <select class="form-select info-box" name="position_id" required>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                                    {{ $pos->name_positions }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{--
                                    <div class="mb-3">
                                        <label class="form-label">Quân hàm</label>
                                        <select class="form-select" name="rank_id" required>
                                            @foreach($ranks as $rank)
                                            <option value="{{ $rank->id }}" {{ $employee->rank_id == $rank->id ? 'selected' : '' }}>
                                                {{ $rank->name_ranks }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    --}}
                                @else
                                    <div class="mb-3">
                                        <div class="position-cus">
                                            <input type="text"
                                                class="info-box form-control cursor_not_allowed {{ in_array($employee->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'high-level' : '' }}"
                                                value="{{ $employee->position->name_positions }}" disabled>
                                        </div>
                                    </div>
                                @endif
                                <label class="form-label"><b>Quân hàm</b></label>
                                @if(auth()->user()->getRoleLevel() >= 1)
                                    <div class="mb-3">
                                        <select class="form-select info-box" name="rank_id" data-original="{{ $employee->rank_id }}"
                                            required>
                                            @foreach($ranks as $rank)
                                                <option value="{{ $rank->id }}" {{ $employee->rank_id == $rank->id ? 'selected' : '' }}>
                                                    {{ $rank->name_ranks }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <input type="text" class="form-control info-box input__view cursor_not_allowed"
                                            value="{{ $employee->rank->name_ranks }}" disabled>
                                    </div>
                                @endif
                                @if (!auth()->user()->isManager())
                                    <button type="submit" id="btnUpdateAvatar" class="btn-update-profile mt-3" disabled>
                                        Cập nhật ảnh đại diện
                                    </button>
                                @else
                                    <button type="submit" id="btnUpdateAvatar" class="btn-update-profile mt-3" disabled>
                                        Cập nhật ảnh đại diện
                                    </button>
                                    <button type="submit" id="btnUpdateProfile" class="btn-update-profile mt-3" disabled>
                                        Cập nhật thông tin
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="card p-5 mt-5 box-info-custom">
                                    <div class="card-body">
                                        <div class="info-box item-info mb-3 text-center">
                                            <strong>
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Tổng Tiền Sự Nghiệp
                                            </strong>
                                            <div class="text-success">{{ number_format($tongTienSuNghiep) }}$</div>
                                        </div>
                                        <div class="info-box item-info mb-3 text-center">
                                            <strong>
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Lương tháng
                                            </strong>
                                            <div class="fst-italic">
                                                <details>
                                                    <summary>Chi Tiết</summary>
                                                    <table class="tb_total_month table table-sm">
                                                        <tbody>
                                                            @foreach(auth()->user()->monthly_attendance_summaries as $summary)
                                                                <tr>
                                                                    <td>{{ str_pad($summary->month, 2, '0', STR_PAD_LEFT) }}/{{ $summary->year }}
                                                                    </td>
                                                                    <td class="text-primary">
                                                                        {{ number_format($summary->total_hours, 2) }}h
                                                                    </td>
                                                                    <td class="text-success">
                                                                        {{ number_format($summary->total_wage) }}$
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </details>
                                            </div>
                                        </div>
                                        <div class="info-box item-info mb-3 text-center">
                                            <strong>
                                                <i class="fa-solid fa-calendar-days"></i> Ngày tham gia website
                                            </strong>
                                            <div>
                                                {{ auth()->user()->created_at->format('h:i:s, d/m/Y')}}
                                            </div>
                                        </div>
                                        <div class="info-box item-info mb-3 text-center">
                                            <strong>
                                                <i class="fa-solid fa-calendar-days"></i> Số ngày tham gia website
                                            </strong>
                                            <div>
                                                {{ number_format(auth()->user()->created_at->diffInDays(now()), 1) }} ngày,
                                                {{ number_format(auth()->user()->created_at->diffInMonths(now()), 1) }} tháng,
                                                {{ number_format(auth()->user()->created_at->diffInYears(now()), 1) }} năm
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-3 d-flex align-items-center h3 gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->employee->name_ingame ?? auth()->user()->username) }}&background=random"
                            alt="/" width="60" height="60" class="rounded-circle">
                        <strong>ID:</strong>{{ auth()->user()->id }}
                        <strong>Role:</strong>{{ auth()->user()->role }}
                        <strong>Username:</strong>{{ auth()->user()->username }}
                        <strong>Create at:</strong>{{ auth()->user()->created_at->format('h:i:s_d/m/Y') }}
                    </div>
                @endif
            </div>
        </form>
        @if($employee)
            <div class="d-flex justify-content-center mt-2 box-connect-discord">
                {{-- Kiểm tra nếu nhân viên đã liên kết Discord --}}
                @if ($employee->discord_id ?? false)
                    <form action="{{ route('discord.unlink') }}" method="POST"
                        onsubmit="return confirm('Bạn chắc chắn muốn hủy liên kết Discord?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Hủy liên kết với <img src="{{ $employee->discord_avatar }}" alt="discord_avt"
                                class="border border-primary rounded-circle" width="30" height="30">
                            {{ $employee->discord_username }}
                        </button>
                    </form>
                    <a href="https://discord.gg/YK3xRYmu" class="btn btn-primary lien-ket-discord ms-2" id="discord-link">
                        <i class="fab fa-discord"></i>Tham Gia <b>Lanyard</b> Hiển Thị Hoạt Động Discord
                    </a>
                @else
                    <a href="{{ route('discord.connect') }}" class="btn btn-primary lien-ket-discord">
                        <i class="fab fa-discord"></i> Liên kết Discord
                    </a>
                @endif
            </div>
        @endif
        @php
            use Carbon\Carbon;
            $discordId = auth()->user()->employee->discord_id ?? 1384541850723155968;
            $discordIdTEST = 918149623133143061;
            $response = Http::get("https://api.lanyard.rest/v1/users/$discordIdTEST");
            $data = $response->json()['data'] ?? [];
            $activities = $data['activities'] ?? [];
            function formatElapsed($startTimestampMs)
            {
                $start = Carbon::createFromTimestamp($startTimestampMs / 1000);

                $seconds = $start->diffInSeconds(Carbon::now());

                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $secs = $seconds % 60;

                return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
            }
        @endphp
        @foreach ($activities as $activity)
            <div class="p-3 mt-2 box-connect-discord" style="max-width: fit-content; border-left: 5px solid #EFB036;">
                {{-- Nếu là custom status --}}
                @if ($activity['type'] === 4)
                    <strong>Status:</strong> {{ $activity['state'] ?? '' }}
                    {{-- Nếu là hoạt động bình thường (game/app) --}}
                @else
                    <strong>Tên app: {{ $activity['name'] }}</strong><br>

                    @if (!empty($activity['details']))
                        <div><b>Chi tiết:</b> {{ $activity['details'] }}</div>
                    @endif

                    @if (!empty($activity['state']))
                        <div><b>Trạng thái: </b>{{ $activity['state'] }}</div>
                    @endif

                    @if (!empty($activity['assets']['large_text']))
                        <div><b>Mô tả:</b> {{ $activity['assets']['large_text'] }}</div>
                    @endif

                    @if (!empty($activity['platform']))
                        <div><b>Nền tảng:</b> {{ $activity['platform'] }}</div>
                    @endif
                    @if (!empty($activity['timestamps']['start']))
                        @php
                            $elapsed = formatElapsed($activity['timestamps']['start']);
                        @endphp

                        <div>
                            <strong>{{ $activity['name'] }}</strong><br>
                            <strong>Đã chạy:</strong> <span>{{ $elapsed }}</span>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
@endsection
@push('scripts')
    <script>
        function confirmDeleteAvatar() {
            Swal.fire({
                title: 'Bạn chắc chắn?',
                text: "Xóa ảnh đại diện sẽ không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Huỷ'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    document.getElementById('deleteAvatarForm').submit();
                }
            });
        }

        document.querySelector('.delete-avatar').addEventListener('submit', function (e) {
            showLoading();
        });
        // document.querySelector('.lien-ket-discord').addEventListener('click', function (e) {
        //     showLoading();
        // });

        document.getElementById('discord-link').addEventListener('click', function (event) {
            event.preventDefault();
            const link = this.href;
            navigator.clipboard.writeText(link).then(() => {
                alert('Copy link thành công!');
            }).catch(err => {
                console.error('Lỗi copying link: ', err);
            });
        });
    </script>
@endpush
@push('scripts')
    <script>
        const btnAvatar = document.getElementById('btnUpdateAvatar');
        const btnProfile = document.getElementById('btnUpdateProfile');

        const avatarInput = document.querySelector('input[name="avatar"]');
        const nameInput = document.querySelector('input[name="name_ingame"]');
        const rankSelect = document.querySelector('select[name="rank_id"]');

        // kiểm tra avatar
        avatarInput.addEventListener('change', function () {
            btnAvatar.disabled = (this.files.length === 0);
        });

        // kiểm tra name và rank
        function checkProfileChanges() {
            const nameChanged = nameInput.value !== nameInput.dataset.original;
            const rankChanged = rankSelect.value !== rankSelect.dataset.original;
            btnProfile.disabled = !(nameChanged || rankChanged);
        }

        nameInput.addEventListener('input', checkProfileChanges);
        rankSelect.addEventListener('change', checkProfileChanges);

        // chạy khi load trang
        checkProfileChanges();
    </script>
@endpush