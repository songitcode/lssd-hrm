<link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ asset('assets/css/nav_marquee_1.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Lilita+One&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

@auth
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="nav-logo">
                    <h5 class="lilita-one-regular">LOS SANTOS SHERIFF'S DEPARTMENT</h5>
                </div>
                <div class="user-info d-flex align-items-center gap-2">
                    <span>
                        Xin chào, <a href="{{ route('profile') }}" class="{{ request()->is('profile') ? 'text-decoration-underline' : '' }}">
                            {{ auth()->user()->employee->rank->name_ranks ?? auth()->user()->role }}
                            <strong>{{ auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin' }}</strong>
                        </a>
                    </span>
                    <div class="nav-avartar">
                        @if (auth()->user()->employee->discord_id ?? false)
                            <img src="{{ auth()->user()->employee->discord_avatar }}"
                                alt="Avatar" class="rounded-circle user-avatar" width="46" height="46"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random';">
                        @elseif (auth()->user()->employee && auth()->user()->employee->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->employee->avatar) }}"
                                alt="Avatar" class="rounded-circle user-avatar" width="46" height="46"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random';">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random"
                                class="rounded-circle user-avatar" alt="Avatar" width="46" height="46">
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn-drop-custom {{ request()->is('profile') ? 'active-link-popup' : '' }}"
                            type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu lssd-card" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item text-primary {{ request()->is('profile') ? 'active-link-popup' : '' }}"
                                    href="{{ route('profile') }}">
                                    <i class="fas fa-id-card me-2"></i>Hồ Sơ Cá Nhân
                                </a>
                            </li>
                            @if(auth()->user()->isManager())
                                <li>
                                    <a class="dropdown-item text-warning" href="{{ route('employees.index') }}">
                                        <i class="fas fa-shield-halved me-2"></i>Trang Quản Lý
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-custom position-sticky top-0">
        <div class="container color-white">
            <a class="navbar-brand d-flex align-items-center logo_lssd" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="Logo" height="50" class="me-3">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('home') ? 'active-link' : '' }}"
                            href="{{ route('home') }}">
                            <i class="fa-solid fa-house"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('attendance') ? 'active-link' : '' }}"
                            href="{{ route('attendance.index') }}">
                            <i class="fa-solid fa-clock"></i> Chấm Công
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('bao-lanh-toi-pham') ? 'active-link' : '' }}"
                            href="{{ route('partials.criminal_bail') }}">
                            <i class="fa-solid fa-gavel"></i> Bảo Lãnh Tội Phạm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ho-tro-xu-an') ? 'active-link' : '' }}"
                            href="{{ route('partials.proc_records') }}">
                            <i class="fa-solid fa-file-lines"></i> Hỗ Trợ Xử Án
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ho-tro-truy-na') ? 'active-link' : '' }}"
                            href="{{ route('partials.wanted_support') }}">
                            <i class="fa-solid fa-magnifying-glass"></i> Hỗ Trợ Truy Nã
                        </a>
                    </li>
                    {{--<li class="nav-item">
                        <a class="nav-link {{ request()->is('don-xin-nghi-phep') ? 'active-link' : '' }}"
                            href="{{ route('partials.take_leave') }}">
                            Đơn Xin Nghỉ Phép
                        </a>
                    </li>--}}

                    {{-- Đội Trưởng / Đội Phó: xem Onduty Live --}}
                    @if(auth()->user()->quanLyOnduty())
                        <li class="nav-item">
                            @php $nguoiDungDangOnduty = \App\Models\Attendance::where('status', 'Đang On-Duty')->count(); @endphp
                            @if ($nguoiDungDangOnduty > 0)
                                <a class="nav-link text-success {{ request()->is('onduty-live') ? 'active-link' : '' }}"
                                    href="{{ route('partials.onduty_live') }}">
                                    <i class="fa-solid fa-circle-dot"></i>
                                    Onduty Live <small class="text-danger">({{ $nguoiDungDangOnduty }})</small>
                                </a>
                            @else
                                <a class="nav-link text-danger {{ request()->is('onduty-live') ? 'active-link' : '' }}"
                                    href="{{ route('partials.onduty_live') }}">
                                    <i class="fa-solid fa-circle-dot"></i> Onduty Live 0
                                </a>
                            @endif
                        </li>
                    @endif
                </ul>

                {{-- Nút chuyển sang trang Admin (chỉ hiện với isManager) --}}
                @if(auth()->user()->isManager())
                    <a href="{{ route('employees.index') }}" class="btn-admin-switch">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Quản Lý</span>
                        <i class="fa-solid fa-arrow-right ms-1" style="font-size: .7rem;"></i>
                    </a>
                    <style>
                        .btn-admin-switch {
                            display: inline-flex;
                            align-items: center;
                            gap: .45rem;
                            padding: .42rem 1rem;
                            background: linear-gradient(135deg, rgba(212,175,55,.18) 0%, rgba(212,175,55,.06) 100%);
                            border: 1.5px solid rgba(212,175,55,.5);
                            border-radius: 8px;
                            color: #d4af37;
                            font-size: .8rem;
                            font-weight: 600;
                            text-decoration: none;
                            letter-spacing: .03em;
                            transition: background .2s, border-color .2s, color .2s;
                        }
                        .btn-admin-switch:hover {
                            background: rgba(212,175,55,.28);
                            border-color: #d4af37;
                            color: #f5cc46;
                        }
                        @media (max-width: 991px) {
                            .btn-admin-switch { margin-top: .75rem; }
                        }
                    </style>
                @endif
            </div>
        </div>
    </nav>
@else
    <a href="{{ route('login') }}">Đăng nhập</a>
@endauth

@push('scripts')
    <script>
        document.addEventListener("click", function (e) {
            const popup = document.querySelector(".popup");
            if (popup) {
                const checkbox = popup.querySelector("input[type=checkbox]");
                if (checkbox && !popup.contains(e.target)) {
                    checkbox.checked = false;
                }
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('loadingOverlay');

            document.querySelectorAll('a[href]').forEach(link => {

                // Bỏ qua các link mở tab mới
                if (link.target === '_blank') return;

                // Bỏ qua javascript:
                if (link.href.startsWith('javascript:')) return;

                link.addEventListener('click', function () {

                    if (overlay) {
                        overlay.style.display = 'flex';
                    }

                });

            });
        });
    </script>
@endpush
