@extends(auth()->user()->isManager() ? 'layouts.admin' : 'layouts.app')

@section('title', auth()->user()->employee->name_ingame ?? 'Hồ Sơ Cá Nhân')@push('styles')
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush

@section('content')
    @php
    use Carbon\Carbon;
        $highRoles = ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'];
        $isHighRole = in_array(auth()->user()->role, $highRoles);
            $discordIdTest = 918149623133143061;
            $discordId = $employee->discord_id ?? 0;
            $response = Http::get("https://lanyard.rest/v1/users/$discordId");
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

    <div class="profile-container">
        {{-- Nút chuyển theme --}}
        <div class="theme-toggle-profile" id="themeToggleProfile">
            <i class="fas fa-moon" id="themeIconProfile"></i>
            <span id="themeTextProfile">Dark Mode</span>
        </div>

        {{-- Form xóa avatar ẩn --}}
        <form id="deleteAvatarForm" action="{{ route('profile.deleteAvatar') }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        {{-- Form chính --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-main-card">
                @if($employee)
                    <div class="row g-4">
                        {{-- Cột trái: Avatar --}}
                        <div class="col-lg-4">
                            <div class="avatar-section">
                                <div class="avatar-wrapper">
                                    <div class="avatar-frame">
                                        @if ($employee->discord_id ?? false)
                                            <img id="avatarPreview" src="{{ $employee->discord_avatar }}" alt="Avatar" class="avatar-img"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'User') }}&background=random';">
                                        @elseif ($employee->avatar)
                                            <img id="avatarPreview" src="{{ asset('storage/' . $employee->avatar) }}" alt="Avatar" class="avatar-img"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'User') }}&background=random';">
                                        @else
                                            <img id="avatarPreview" src="https://ui-avatars.com/api/?name={{ urlencode($employee->name_ingame ?? 'User') }}&background=random"
                                                class="avatar-img" alt="Avatar">
                                        @endif
                                    </div>
                                    {{-- Trang trí overlay (tuỳ chọn) --}}
                                    <div class="avatar-overlay-decoration"></div>
                                </div>

                                @if (!$employee->discord_id ?? false)
                                    <input type="file" name="avatar" accept="image/*" class="d-none" id="avatarInput" onchange="previewAvatar(event)">
                                    <label for="avatarInput" class="btn-upload-avatar">
                                        <i class="fa fa-camera"></i> Chọn ảnh đại diện
                                    </label>
                                @else
                                    <p class="btn-upload-avatar" style="border-style: solid; background: var(--panel-bg); cursor: default;">
                                        <i class="fab fa-discord"></i> {{ $employee->discord_username }}
                                    </p>
                                @endif

                                @if($employee->avatar)
                                    <button type="button" onclick="confirmDeleteAvatar()" class="btn-remove-avatar">
                                        <i class="fa fa-trash-alt"></i> Xoá ảnh đại diện
                                    </button>
                                @endif

                                <div class="status-">
                                    {{-- Hiển thị Activity từ Lanyard (demo với ID test) --}}
                                    @if(count($activities))
                                        <div class="mt-4">
                                            <h6 style="color: var(--gold-dim);"><i class="fa-solid fa-gamepad me-2"></i>Discord Activities</h6>
                                            @foreach ($activities as $activity)
                                                <div class="activity-card" style="width: fit-content; font-size: 11px; font-weight: 100;">
                                                    @if ($activity['type'] === 4)
                                                        <span><i class="fa-solid fa-circle text-success"></i> {{ $activity['state'] ?? 'notyet' }}
                                                    @else
                                                        <strong>{{ $activity['name'] }}</strong><br>
                                                        @if (!empty($activity['details']))
                                                            <span>{{ $activity['details'] }}</span><br>
                                                        @endif
                                                        @if (!empty($activity['state']))
                                                            <span>{{ $activity['state'] }}</span><br>
                                                        @endif
                                                        @if (!empty($activity['timestamps']['start']))
                                                            <span><i class="fa-solid fa-clock"></i> {{ formatElapsed($activity['timestamps']['start']) }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Cột phải: Form thông tin --}}
                        <div class="col-lg-8">
                            <h3 style="font-family: 'Oswald', sans-serif; color: var(--gold-dim); letter-spacing: 2px; margin-bottom: 25px;">
                                <i class="fa fa-id-card me-2"></i>HỒ SƠ SĨ QUAN
                            </h3>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="form-label-custom">Tên đăng nhập</div>
                                        <input type="text" class="input-custom" value="{{ $employee->user->username }}" disabled>
                                    </div>
                                    <div class="mb-4">
                                        <div class="row">
                                            <div class="col-6">
                                                 <div class="form-label-custom">ID Discord</div>
                                                    @if ($employee->discord_id)
                                                        <input type="text" class="input-custom" value="{{ $employee->discord_id }}" disabled>
                                                    @else
                                                        <input type="text" class="input-custom" value="Chưa liên kết" disabled>
                                                    @endif
                                            </div>
                                            <div class="col-6">
                                                <div class="form-label-custom">Discord username</div>
                                                    @if ($employee->discord_id)
                                                        <input type="text" class="input-custom" value="{{ $employee->discord_username }}" disabled>
                                                    @else
                                                        <input type="text" class="input-custom" value="Chưa liên kết" disabled>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="form-label-custom">Tên trong game (GTA5VN)</div>
                                        @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'trợ lý cục trưởng']))
                                            <input type="text" class="input-custom" value="{{ $employee->name_ingame }}" readonly disabled>
                                        @else
                                            <input type="text" name="name_ingame" class="input-custom" value="{{ $employee->name_ingame }}"
                                                data-original="{{ $employee->name_ingame }}" required>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-label-custom">Chức vụ</div>
                                        @if($canEditPosition)
                                            <select class="select-custom" name="position_id" required>
                                                @foreach($positions as $pos)
                                                    <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                                        {{ $pos->name_positions }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="input-custom highlight" value="{{ $employee->position->name_positions }}" disabled>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-label-custom">Quân hàm</div>
                                        @if(auth()->user()->getRoleLevel() >= 1)
                                            <select class="select-custom" name="rank_id" data-original="{{ $employee->rank_id }}" required>
                                                @foreach($ranks as $rank)
                                                    <option value="{{ $rank->id }}" {{ $employee->rank_id == $rank->id ? 'selected' : '' }}>
                                                        {{ $rank->name_ranks }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="input-custom" value="{{ $employee->rank->name_ranks }}" disabled>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-item text-center">
                                            <strong><i class="fa-solid fa-sack-dollar me-1"></i>Tổng tiền sự nghiệp</strong>
                                            <div class="info-value text-success">{{ number_format($tongTienSuNghiep) }}$</div>
                                        </div>
                                        {{--<div class="info-item text-center salary-details">
                                            <strong><i class="fa-solid fa-calendar-alt me-1"></i>Lương</strong>
                                            <details>
                                                <summary style="cursor: pointer; color: var(--gold-dim);">Xem chi tiết</summary>
                                                <table class="mt-2">
                                                    @foreach(auth()->user()->monthly_attendance_summaries as $summary)
                                                        <tr>
                                                            <td>{{ str_pad($summary->month, 2, '0', STR_PAD_LEFT) }}/{{ $summary->year }}</td>
                                                            <td class="text-primary">{{ number_format($summary->total_hours, 2) }}h</td>
                                                            <td class="text-success">{{ number_format($summary->total_wage) }}$</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </details>
                                        </div>--}}
                                        <div class="info-item text-center">
                                            <strong><i class="fa-regular fa-calendar-check me-1"></i>Ngày tham gia</strong>
                                            <div>{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">
                                                {{ auth()->user()->created_at->diffInDays(now()) }} ngày
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Nút cập nhật --}}
                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" id="btnUpdateAvatar" class="btn-update" disabled>
                                    <i class="fa fa-image me-2"></i>Cập nhật ảnh
                                </button>
                                @if(auth()->user()->isManager())
                                    <button type="submit" id="btnUpdateProfile" class="btn-update" disabled>
                                        <i class="fa fa-save me-2"></i>Cập nhật thông tin
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Fallback khi không có employee --}}
                    <div class="d-flex align-items-center gap-3 p-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->username) }}&background=random"
                            width="60" height="60" class="rounded-circle">
                        <div>
                            <strong>ID:</strong> {{ auth()->user()->id }}<br>
                            <strong>Role:</strong> {{ auth()->user()->role }}<br>
                            <strong>Username:</strong> {{ auth()->user()->username }}
                        </div>
                    </div>
                @endif
            </div>
        </form>

        {{-- Discord liên kết --}}
        @if($employee)
            <div class="discord-connect-card">
                <div>
                    <h5 style="color: var(--gold-dim);"><i class="fab fa-discord me-2"></i>Kết nối Discord</h5>
                    @if ($employee->discord_id ?? false)
                        <p class="mb-0">Đã liên kết với <strong>{{ $employee->discord_username }}</strong></p>
                    @else
                        <p class="mb-0">Liên kết tài khoản Discord để hiển thị hoạt động và avatar tự động.</p>
                    @endif
                </div>
                <div>
                    @if ($employee->discord_id ?? false)
                        <form action="{{ route('discord.unlink') }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Bạn chắc chắn muốn hủy liên kết Discord?')">
                            @csrf
                            <button type="submit" class="btn-discord btn-discord-danger">
                                <i class="fab fa-discord"></i> Huỷ liên kết
                            </button>
                        </form>
                        <a href="https://discord.gg/JeSrQWvTUy" class="btn-discord ms-2" target="_blank">
                            <i class="fab fa-discord"></i> Tham gia Lanyard
                        </a>
                    @else
                        <a href="{{ route('discord.connect') }}" class="btn-discord">
                            <i class="fab fa-discord"></i> Liên kết Discord
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {{-- Các script giữ nguyên --}}
    <script>
        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('avatarPreview').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function confirmDeleteAvatar() {
            Swal.fire({
                title: 'Xác nhận xoá?',
                text: "Ảnh đại diện sẽ bị xoá vĩnh viễn.",
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

        // Kích hoạt nút cập nhật khi có thay đổi
        const avatarInput = document.querySelector('input[name="avatar"]');
        const btnAvatar = document.getElementById('btnUpdateAvatar');
        if(avatarInput) {
            avatarInput.addEventListener('change', () => btnAvatar.disabled = false);
        }

        const nameInput = document.querySelector('input[name="name_ingame"]');
        const rankSelect = document.querySelector('select[name="rank_id"]');
        const btnProfile = document.getElementById('btnUpdateProfile');
        if(btnProfile && nameInput && rankSelect) {
            function checkProfileChange() {
                const nameChanged = nameInput.value !== nameInput.dataset.original;
                const rankChanged = rankSelect.value !== rankSelect.dataset.original;
                btnProfile.disabled = !(nameChanged || rankChanged);
            }
            nameInput.addEventListener('input', checkProfileChange);
            rankSelect.addEventListener('change', checkProfileChange);
        }

        // Theme toggle
        (function() {
            const toggleBtn = document.getElementById('themeToggleProfile');
            const icon = document.getElementById('themeIconProfile');
            const text = document.getElementById('themeTextProfile');
            const html = document.documentElement;
            const saved = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', saved);
            updateUI(saved);
            toggleBtn.addEventListener('click', () => {
                const current = html.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                updateUI(next);
            });
            function updateUI(theme) {
                if (theme === 'dark') {
                    icon.className = 'fas fa-sun';
                    text.textContent = 'Light Mode';
                } else {
                    icon.className = 'fas fa-moon';
                    text.textContent = 'Dark Mode';
                }
            }
        })();

        // Copy link (nếu cần)
        document.getElementById('discord-link')?.addEventListener('click', function(e) {
            e.preventDefault();
            navigator.clipboard.writeText(this.href).then(() => alert('Đã copy link!'));
        });
    </script>
@endpush