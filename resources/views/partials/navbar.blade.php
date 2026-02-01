<link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ asset('assets/css/nav_marquee_1.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=BBH+Sans+Bogle&family=Lilita+One&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

@auth
<!-- <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom"></nav> -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="nav-logo">
                    <!-- <img src="{{ asset('assets/images/banner_lssd.png') }}" width="230" height="auto" alt=""> -->
                    <h5 class="lilita-one-regular">LOS SANTOS SHERIFF'S DEPARTMENT</h5>
                </div>
                <div class="user-info d-flex align-items-center gap-2">
                    <span>
                        Xin chào, <a href="{{ route('profile') }}" class="{{ request()->is('profile') ? 'text-decoration-underline' : '' }}"> {{ auth()->user()->employee->rank->name_ranks ?? auth()->user()->role }}
                        <strong>{{ auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin' }}</strong>
                        </a>
                    </span>
                    <div class="nav-avartar">
                        @if (auth()->user()->employee->discord_id ?? false)
                            <img src="{{ auth()->user()->employee->discord_avatar }}"
                                alt="Avatar"
                                class="rounded-circle user-avatar"
                                width="46"
                                height="46"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random';">
                        @elseif (auth()->user()->employee && auth()->user()->employee->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->employee->avatar) }}"
                                alt="Avatar"
                                class="rounded-circle user-avatar"
                                width="46"
                                height="46"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random';">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->employee?->name_ingame ?? auth()->user()?->username ?? 'Admin') }}&background=random"
                                class="rounded-circle user-avatar"
                                alt="Avatar"
                                width="46"
                                height="46">
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn-drop-custom {{ request()->is('profile') ? 'active-link-popup' : '' }}" type="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu lssd-card" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item text-primary {{ request()->is('profile') ? 'active-link-popup' : '' }}" href="{{ route('profile') }}">
                                    <i class="fas fa-info-circle me-2"></i>Hồ sơ
                                </a>
                            </li>
                            <!-- <li><a class="dropdown-item text-success" href="#"><i class="fas fa-cog me-2"></i>Cài đặt</a></li> -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- <div class="marquee-wrap" role="region" aria-label="Thông báo">
                <div class="container">
                    <div class="marquee color-info" tabindex="0">
                        🔔 <strong>Thông báo</strong>: Nhằm quản lý hoạt động On Duty của các bạn, mọi người vui lòng vào hồ sơ liên kết Discord và tham gia Bot Lanyard
                        &nbsp;&nbsp;-&nbsp;&nbsp;
                        Mọi Bug, Lag, ... thắc mắc vui lòng liên hệ Son Myname trên Discord LSSD để được hỗ trợ nhanh nhất !
                    </div>
                </div>
            </div> -->
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-custom position-sticky top-0">
        <div class="container color-white">
            <a class="navbar-brand d-flex align-items-center logo_lssd" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="Logo" height="50" class="me-3">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('home') ? 'active-link' : '' }}"
                            href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('attendance') ? 'active-link' : '' }}"
                            href="{{ route('attendance.index') }}"><i class="fa-solid fa-clock"></i> Chấm Công</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('bao-lanh-toi-pham') ? 'active-link' : '' }}"
                            href="{{ route('partials.criminal_bail') }}">Bảo Lãnh Tội Phạm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ho-tro-xu-an') ? 'active-link' : '' }}"
                            href="{{ route('partials.proc_records') }}">Hỗ Trợ Xử Án</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ho-tro-truy-na') ? 'active-link' : '' }}"
                            href="{{ route('partials.wanted_support') }}">Hỗ Trợ Truy Nã</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('don-xin-nghi-phep') ? 'active-link' : '' }}"
                            href="{{ route('partials.take_leave') }}">Đơn Xin Nghỉ Phép</a>
                    </li>
                    @if(auth()->user()->quanLyOnduty())
                        <li class="nav-item">
                            @php
                                $nguoiDungDangOnduty = \App\Models\Attendance::where('status', 'Đang On-Duty')->count();
                            @endphp
                            @if ($nguoiDungDangOnduty > 0)
                            <a class="nav-link text-success {{ request()->is('onduty') ? 'active-link' : '' }}"
                            href="{{ route('partials.ondutyList') }}">Onduty Live <small class="text-danger">({{ $nguoiDungDangOnduty}})</small></a>
                            @else
                            <a class="nav-link text-danger {{ request()->is('onduty') ? 'active-link' : '' }}"
                            href="{{ route('partials.ondutyList') }}">Onduty Live 0</a>
                            @endif
                        </li>
                    @endif
                    
                    @if(auth()->user()->isManager())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Quản Lý
                            </a>
                            <ul class="dropdown-menu p-2 lssd-card" aria-labelledby="navbarDropdown" style="width: fit-content !important;">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('employees') ? 'active-link' : '' }}"
                                        href="{{ route('employees.index') }}"><i class="fa-solid fa-users"></i> Nhân Sự</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('salary-configs') ? 'active-link' : '' }}"
                                        href="{{ route('salary_configs.index') }}"><i class="fa-solid fa-scale-unbalanced"></i> Hệ Số Lương</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('payroll') ? 'active-link' : '' }}"
                                        href="{{ route('payroll.index') }}"><i class="fa-solid fa-sack-dollar"></i> Công/ Lương</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{-- request()->is('office-admin') ? 'active-link' : '' --}}"
                                        href="{{-- route('partials.office_members') --}}"><i class="fa-solid fa-sack-dollar"></i> Bảng Nhân Sự</a>
                                </li>
                                <li class="nav-item">
                                    @php
                                        $nguoiDungDangOnduty = \App\Models\Attendance::where('status', 'Đang On-Duty')->count();
                                    @endphp
                                    @if ($nguoiDungDangOnduty > 0)
                                        <a class="nav-link {{ request()->is('onduty') ? 'active-link' : '' }}"
                                        href="{{ route('partials.ondutyList') }}"><span class="rounded-circle bg-danger text-white" style="padding: 3px 6px; font-size: 13px;">{{ $nguoiDungDangOnduty}}</span> Onduty LIVE</a>
                                    @else
                                        <a class="nav-link {{ request()->is('onduty') ? 'active-link' : '' }}"
                                        href="{{ route('partials.ondutyList') }}"><i class="fa-solid fa-circle-dot" style="color: #00d904ff;"></i> Onduty LIVE</a>
                                    @endif
                                </li>
                            </ul>
                        </li>

                        {{-- @else
                        <div class="text-white bg-warning p-3">Bạn không có quyền vào</div> --}}
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@else
    <a href="{{ route('login') }}">Đăng nhập</a>
@endauth

@push('scripts')
    <script>
        // let navGsap = gsap.timeline();
        // navGsap.from(".main-header, .navbar", {y: -100, duration: 1, opacity: 0 , ease: "power1.inOut"});
        // navGsap.from(".navbar-nav .nav-item", {y: -30, opacity: 0, duration: 0.5, stagger: 0.1, ease: "power1.inOut"});

        document.addEventListener("click", function (e) {
            const popup = document.querySelector(".popup");
            const checkbox = popup.querySelector("input[type=checkbox]");
            if (!popup.contains(e.target)) {
                checkbox.checked = false;
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const xemBtns = document.querySelectorAll('.btn-logout');

            xemBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    document.getElementById('loadingOverlay').style.display = 'flex';
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const xemBtns = document.querySelectorAll('.loader-1');

            xemBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    document.getElementById('loadingOverlay').style.display = 'flex';
                });
            });
        });
    </script>
@endpush