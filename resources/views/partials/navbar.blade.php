<link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
@auth
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
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
                            href="{{ route('home') }}">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('attendance') ? 'active-link' : '' }}"
                            href="{{ route('attendance.index') }}">Chấm Công 0.1</a>
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
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#">Ban Lãnh Đạo</a>
                    </li> --}}
                    @if(auth()->user()->isManager())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Quản Lý
                            </a>
                            <ul class="dropdown-menu p-2" aria-labelledby="navbarDropdown">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('employees') ? 'active-link' : '' }}"
                                        href="{{ route('employees.index') }}">Nhân Sự</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('salary-configs') ? 'active-link' : '' }}"
                                        href="{{ route('salary_configs.index') }}">Hệ Số Lương</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('payroll') ? 'active-link' : '' }}"
                                        href="{{ route('payroll.index') }}">Công/ Lương</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('onduty') ? 'active-link' : '' }}"
                                        href="{{ route('partials.ondutyList') }}">On-duty LIVE</a>
                                </li>
                            </ul>
                        </li>

                        {{-- @else
                        <div class="text-white bg-warning p-3">Bạn không có quyền vào</div> --}}
                    @endif
                </ul>

                <div class="d-flex align-items-center navbar-right">
                    <label class="popup">
                        <input type="checkbox" />
                        <div tabindex="0" class="burger">
                            @if (auth()->user()->employee && auth()->user()->employee->avatar)
                                <img src="{{ asset('storage/' . optional(auth()->user()->employee)->avatar) }}" alt="Avatar"
                                    class="rounded-circle" width="41" height="41">
                            @else
                                <i class="fa-solid fa-user fa-xl"></i>
                            @endif
                        </div>
                        <nav class="popup-window">
                            <legend>Tài Khoản</legend>
                            <ul>
                                <li>
                                    <a class="nav-links {{ request()->is('profile') ? 'active-link-popup' : '' }}"
                                        href="{{ route('profile') }}">
                                        <button>
                                            <i class="fas fa-info-circle"></i>
                                            <span>Hồ Sơ</span>
                                        </button>
                                    </a>
                                </li>
                                <li>
                                    <button>
                                        <i class="fas fa-cog"></i>
                                        <span>Cài Đặt</span>
                                    </button>
                                </li>
                                <hr>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="btn-logout" type="submit">
                                            <i class="fas fa-lock"></i>
                                            <span>Đăng Xuất</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </label>
                    <span class="display-name">
                        <a href="{{ route('profile') }}"
                            class="nav-link">{{ auth()->user()->employee->name_ingame ?? 'ADMIN' }}</a>
                    </span>
                </div>
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
            const checkbox = popup.querySelector("input[type=checkbox]");
            if (!popup.contains(e.target)) {
                checkbox.checked = false;
            }
        });
    </script>
@endpush