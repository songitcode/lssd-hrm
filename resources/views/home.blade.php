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

</style>
@endpush

@php
$maintenance = false;
$quockhanh2026 = false;
@endphp

@section('content')
@if($maintenance && !View::hasSection('hide_maintenance'))
<div class="container align-items-center justify-content-center d-flex mt-5">
    <p>CLOSE</p>
</div>
@else
<div class="home-page container">
    @if($quockhanh2026 && !View::hasSection('hide_quockhanh2026'))
    @include('quockhanh2026')
    @endif

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

    /* ── Quốc khánh 02/09 Countdown ── */
    (function() {
        const target = new Date('2026-09-02T00:00:00+07:00').getTime();
        const countdown = document.getElementById('nationalDayCountdown');
        if (!countdown) return;

        const days = document.getElementById('cdDays');
        const hours = document.getElementById('cdHours');
        const minutes = document.getElementById('cdMinutes');
        const seconds = document.getElementById('cdSeconds');
        const title = document.querySelector('.tet-29-countdown-title');
        const live = document.querySelector('.tet-29-countdown-live');

        function pad(value) {
            return String(value).padStart(2, '0');
        }

        function updateCountdown() {
            const remaining = Math.max(0, target - Date.now());

            if (remaining === 0) {
                days.textContent = '00';
                hours.textContent = '00';
                minutes.textContent = '00';
                seconds.textContent = '00';
                if (title) title.innerHTML = 'CHÀO MỪNG <span>QUỐC KHÁNH 02/09</span>';
                if (live) live.innerHTML = '<span></span> ĐÃ ĐẾN NGÀY ĐẠI LỄ';
                countdown.classList.add('is-celebrating');
                return;
            }

            const totalSeconds = Math.floor(remaining / 1000);
            days.textContent = pad(Math.floor(totalSeconds / 86400));
            hours.textContent = pad(Math.floor((totalSeconds % 86400) / 3600));
            minutes.textContent = pad(Math.floor((totalSeconds % 3600) / 60));
            seconds.textContent = pad(totalSeconds % 60);
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();

    /* ── Fireworks Effect ── */
    function createFirework() {
        const fw = document.createElement('div');
        fw.classList.add('firework');
        const colors = ['#FFD447', '#FFFFFF', '#E7B423', '#FF5B64', '#D9192E'];
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
    setInterval(createFirework, 650);
</script>
@endpush