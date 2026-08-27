@extends('layouts.app')

@section('title', 'Trang Chủ')

@push('styles')
{{-- Google Fonts: Cinzel (display) + Inter (body) + JetBrains Mono (data) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
<style>
    .loader {
        --main-size: 4em;
        --text-color: #000000;
        --shine-color: #0000;
        --shadow-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        user-select: none;
        position: relative;
        font-size: var(--main-size);
        font-weight: 900;
        text-transform: uppercase;
        color: var(--text-color);
        width: 7.3em;
        height: 1em;
        filter: drop-shadow(0 0 0.05em var(--shine-color));
    }

    .loader .text {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        position: absolute;
    }

    .loader .text:nth-child(1) {
        clip-path: polygon(0% 0%, 11.11% 0%, 11.11% 100%, 0% 100%);
        font-size: calc(var(--main-size) / 20);
        margin-left: -2.1em;
        opacity: 0.6;
    }

    .loader .text:nth-child(2) {
        clip-path: polygon(11.11% 0%, 22.22% 0%, 22.22% 100%, 11.11% 100%);
        font-size: calc(var(--main-size) / 16);
        margin-left: -0.98em;
        opacity: 0.7;
    }

    .loader .text:nth-child(3) {
        clip-path: polygon(22.22% 0%, 33.33% 0%, 33.33% 100%, 22.22% 100%);
        font-size: calc(var(--main-size) / 13);
        margin-left: -0.33em;
        opacity: 0.8;
    }

    .loader .text:nth-child(4) {
        clip-path: polygon(33.33% 0%, 44.44% 0%, 44.44% 100%, 33.33% 100%);
        font-size: calc(var(--main-size) / 11);
        margin-left: -0.05em;
        opacity: 0.9;
    }

    .loader .text:nth-child(5) {
        clip-path: polygon(44.44% 0%, 55.55% 0%, 55.55% 100%, 44.44% 100%);
        font-size: calc(var(--main-size) / 10);
        margin-left: 0em;
        opacity: 1;
    }

    .loader .text:nth-child(6) {
        clip-path: polygon(55.55% 0%, 66.66% 0%, 66.66% 100%, 55.55% 100%);
        font-size: calc(var(--main-size) / 11);
        margin-left: 0.05em;
        opacity: 0.9;
    }

    .loader .text:nth-child(7) {
        clip-path: polygon(66.66% 0%, 77.77% 0%, 77.77% 100%, 66.66% 100%);
        font-size: calc(var(--main-size) / 13);
        margin-left: 0.33em;
        opacity: 0.8;
    }

    .loader .text:nth-child(8) {
        clip-path: polygon(77.77% 0%, 88.88% 0%, 88.88% 100%, 77.77% 100%);
        font-size: calc(var(--main-size) / 16);
        margin-left: 0.98em;
        opacity: 0.7;
    }

    .loader .text:nth-child(9) {
        clip-path: polygon(88.88% 0%, 100% 0%, 100% 100%, 88.88% 100%);
        font-size: calc(var(--main-size) / 20);
        margin-left: 2.1em;
        opacity: 0.6;
    }

    .loader .text span {
        animation:
            scrolling 2s cubic-bezier(0.1, 0.6, 0.9, 0.4) infinite,
            shadow 2s cubic-bezier(0.1, 0.6, 0.9, 0.4) infinite;
    }

    .loader .text:nth-child(1) span {
        background: linear-gradient(to right,
                var(--text-color) 4%,
                var(--shadow-color) 7%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(2) span {
        background: linear-gradient(to right,
                var(--text-color) 9%,
                var(--shadow-color) 13%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(3) span {
        background: linear-gradient(to right,
                var(--text-color) 15%,
                var(--shadow-color) 18%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(4) span {
        background: linear-gradient(to right,
                var(--text-color) 20%,
                var(--shadow-color) 23%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(6) span {
        background: linear-gradient(to right,
                var(--shadow-color) 29%,
                var(--text-color) 32%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(7) span {
        background: linear-gradient(to right,
                var(--shadow-color) 34%,
                var(--text-color) 37%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(8) span {
        background: linear-gradient(to right,
                var(--shadow-color) 39%,
                var(--text-color) 42%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .text:nth-child(9) span {
        background: linear-gradient(to right,
                var(--shadow-color) 45%,
                var(--text-color) 48%);
        background-size: 200% auto;
        background-clip: text;
        color: transparent;
    }

    .loader .line {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        height: 0.05em;
        width: calc(var(--main-size) / 2);
        margin-top: 0.9em;
        border-radius: 0.05em;
    }

    .loader .line::before {
        content: "";
        position: absolute;
        height: 100%;
        width: 100%;
        background-color: var(--text-color);
        opacity: 0.3;
    }

    .loader .line::after {
        content: "";
        position: absolute;
        height: 100%;
        width: 100%;
        background-color: var(--text-color);
        border-radius: 0.05em;
        transform: translateX(-90%);
        animation: wobble 2s cubic-bezier(0.5, 0.8, 0.5, 0.2) infinite;
    }

    @keyframes wobble {
        0% {
            transform: translateX(-90%);
        }

        50% {
            transform: translateX(90%);
        }

        100% {
            transform: translateX(-90%);
        }
    }

    @keyframes scrolling {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    @keyframes shadow {
        0% {
            background-position: -98% 0;
        }

        100% {
            background-position: 102% 0;
        }
    }
</style>
@endpush

@php
$maintenance = false;
@endphp

@section('content')
@if($maintenance && !View::hasSection('hide_maintenance'))
<div class="container align-items-center justify-content-center d-flex"">
    <div class="loader">
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="text"><span>CLOSE</span></div>
        <div class="line"></div>
    </div>
</div>
@else
<div class="home-page container">
    {{-- ═══════════════════════════════════════
             STATS BAR
             ═══════════════════════════════════════ --}}
    <div class="stats-bar mt-5">
        <div class="stat-pill">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num">{{ $tongSoNhanVien }}</div>
                <div class="stat-lbl">Thành Viên LSSD</div>
            </div>
        </div>

        <div class="stat-divider"></div>

        <div class="stat-pill">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num">{{ $soNhanVienCapCao }}</div>
                <div class="stat-lbl">Thành Viên Cấp Cao</div>
            </div>
        </div>

        <div class="stat-divider"></div>

        <div class="stat-pill">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num">LLCS</div>
                <div class="stat-lbl">Lực Lượng Chấp Sự</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
             MAIN GRID — Table + Right Column
             ═══════════════════════════════════════ --}}
    <div class="row g-4 align-items-start">

        {{-- LEFT: Member Table --}}
        <div class="col-lg-8">
            <div class="main-panel">
                <div class="panel-header">
                    <div>
                        <span class="panel-eyebrow">NHÂN SỰ · LLCS LSSD</span>
                        <h4 class="panel-title-main">Thành Viên Sĩ Quan</h4>
                    </div>
                    <span class="live-badge">
                        <span class="live-dot"></span>ACTIVE
                    </span>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-sm text-center table_employees table-hover">
                        <thead>
                            <tr class="head-table-employees-home">
                                <th>#</th>
                                <th>AVT</th>
                                <th>Tên Sĩ Quan</th>
                                <th>Chức Vụ</th>
                                <th>Quân Hàm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                            <tr class="{{ in_array($user->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'glow' : '' }}">
                                <td class="td-num">{{ $index + 1 }}</td>
                                <td>
                                    @if($user->employee?->discord_id)
                                    <img src="{{ $user->employee->discord_avatar }}"
                                        alt="Avatar"
                                        class="member-avatar"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=2a2a35&color=E8A800';">
                                    @else
                                    @if ($user->employee?->avatar)
                                    <img src="{{ asset('storage/' . $user->employee->avatar) }}"
                                        alt="Avatar"
                                        class="member-avatar"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=2a2a35&color=E8A800';">
                                    @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=2a2a35&color=E8A800"
                                        class="member-avatar"
                                        alt="Avatar">
                                    @endif
                                    @endif
                                </td>
                                <td class="td-name">{{ $user->employee?->name_ingame ?? 'N/A' }}</td>
                                <td>
                                    <span class="position-badge">
                                        {{ $user->employee?->position?->name_positions ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="rank-text">{{ $user->employee?->rank?->name_ranks ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3 phan_trang">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- RIGHT: Welcome + Banner --}}
        <div class="col-lg-4">

            {{-- Welcome Card --}}
            <div class="welcome-card">
                <div class="welcome-card-bg"></div>
                <div class="welcome-content">
                    <div class="welcome-org-badge">LLCS LSSD</div>
                    <p class="welcome-heading">Chào mừng bạn đến với Hệ Thống LLCS LSSD!</p>
                    <p class="welcome-sub">Xem các thông tin và cập nhật mới nhất trên Discord LSSD.</p>
                    <p class="welcome-sub">Chúc bạn một ngày làm việc đầy hiệu quả.</p>
                </div>
            </div>

            {{-- Banner --}}
            <div class="banner-card mt-3">
                <img src="{{ asset('assets/images/banner_lssd.png') }}" alt="LSSD Banner">
                <div class="banner-overlay"></div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         FEATURES SECTION — Tính Năng
         ═══════════════════════════════════════════ --}}
    <div class="container features-section mt-5">
        <div class="features-header">
            <span class="features-eyebrow">CÔNG CỤ HÀNH CHÍNH · LLCS</span>
            <h4 class="features-title">Tính Năng</h4>
        </div>

        <div class="features-grid">

            {{-- Chấm Công --}}
            <a class="feature-card" href="{{ route('attendance.index') }}" target="_blank">
                <div class="feature-glow-line"></div>
                <div class="feature-icon-wrap">
                    <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="30" height="30">
                </div>
                <div class="feature-card-body">
                    <span class="feature-title">Chấm Công</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Bảo Lãnh Tội Phạm --}}
            <a class="feature-card" href="{{ route('partials.criminal_bail') }}" target="_blank">
                <div class="feature-glow-line"></div>
                <div class="feature-icon-wrap">
                    <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="30" height="30">
                </div>
                <div class="feature-card-body">
                    <span class="feature-title">Bảo Lãnh Tội Phạm</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Bộ Luật Liên Bang --}}
            <a class="feature-card"
                href="https://sites.google.com/view/info-gta5vn/b%E1%BB%99-lu%E1%BA%ADt-los-santos?authuser=0"
                target="_blank">
                <div class="feature-glow-line"></div>
                <div class="feature-icon-wrap">
                    <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="30" height="30">
                </div>
                <div class="feature-card-body">
                    <span class="feature-title">Bộ Luật Liên Bang</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Đơn Xin Nghỉ Phép --}}
            <a class="feature-card {{ request()->is('don-xin-nghi-phep') ? 'active-link' : '' }}"
                href="{{ route('partials.take_leave') }}" target="_blank">
                <div class="feature-glow-line"></div>
                <div class="feature-icon-wrap">
                    <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="30" height="30">
                </div>
                <div class="feature-card-body">
                    <span class="feature-title">Đơn Xin Nghỉ Phép</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Văn Hóa Đồn (image modal) --}}
            <a class="feature-card feature-card-img"
                data-bs-toggle="modal" data-bs-target="#imageModal"
                onclick="openImageModal('{{ asset('assets/images/VAN_HOA_LSSD.png') }}')">
                <img src="{{ asset('assets/images/VAN_HOA_LSSD.png') }}"
                    class="feature-bg-img" alt="Văn Hóa Đồn">
                <div class="feature-img-overlay"></div>
                <div class="feature-card-body feature-card-body-img">
                    <span class="feature-title">Văn Hóa Đồn</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Nguyên Tắc Ngành (image modal) --}}
            <a class="feature-card feature-card-img"
                data-bs-toggle="modal" data-bs-target="#imageModal"
                onclick="openImageModal('{{ asset('assets/images/dieu_cam__nguyen_tac_nganh.png') }}')">
                <img src="{{ asset('assets/images/dieu_cam__nguyen_tac_nganh.png') }}"
                    class="feature-bg-img" alt="Nguyên Tắc Ngành">
                <div class="feature-img-overlay"></div>
                <div class="feature-card-body feature-card-body-img">
                    <span class="feature-title">Nguyên Tắc Ngành</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

            {{-- Quy Tắc Súng Điện (image modal) --}}
            <a class="feature-card feature-card-img"
                data-bs-toggle="modal" data-bs-target="#imageModal"
                onclick="openImageModal('{{ asset('assets/images/luat_sung_dien.png') }}')">
                <img src="{{ asset('assets/images/luat_sung_dien.png') }}"
                    class="feature-bg-img" alt="Quy Tắc Súng Điện">
                <div class="feature-img-overlay"></div>
                <div class="feature-card-body feature-card-body-img">
                    <span class="feature-title">Quy Tắc Súng Điện</span>
                    <span class="feature-arrow">→</span>
                </div>
            </a>

        </div>
    </div>

    {{-- ───────────── IMAGE MODAL ───────────── --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-img-content border-0">
                <div class="modal-body text-center p-2">
                    <img id="modalImg" src="" class="img-fluid" style="border-radius: 12px; max-height: 85vh;">
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

<script src="https://cdn.jsdelivr.net/gh/Sean-93/newmarquee@v0.9.1/dist/newmarquee-min.js"></script>

@push('scripts')
<script>
    /* ── Popup close on outside click ── */
    document.addEventListener("click", function(e) {
        const popup = document.querySelector(".popup");
        if (popup) {
            const checkbox = popup.querySelector("input[type=checkbox]");
            if (checkbox && !popup.contains(e.target)) checkbox.checked = false;
        }
    });

    /* ── Image Modal ── */
    function openImageModal(src) {
        document.getElementById("modalImg").src = src;
    }

    /* ── Fireworks Effect ── */
    function createFirework() {
        const fw = document.createElement('div');
        fw.classList.add('firework');
        const colors = ['#FFD04D', '#E8A800', '#ffffff', '#FFB800'];
        fw.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        fw.style.left = Math.random() * 100 + 'vw';
        fw.style.top = Math.random() * 100 + 'vh';
        fw.style.setProperty('--x', (Math.random() - 0.5) * 280 + 'px');
        fw.style.setProperty('--y', (Math.random() - 0.5) * 280 + 'px');
        document.body.appendChild(fw);
        setTimeout(() => fw.remove(), 1000);
    }

    /* Burst on page load */
    for (let i = 0; i < 18; i++) setTimeout(createFirework, i * 140);

    /* Click anywhere */
    document.addEventListener("click", createFirework);

    /* Periodic ambient fireworks */
    setInterval(createFirework, 300);
</script>
@endpush