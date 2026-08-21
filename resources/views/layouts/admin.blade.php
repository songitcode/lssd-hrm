<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/Logo_LSCSD.png') }}">
    <title>@yield('title') — LSSD Quản lý</title>

    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.7-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-6.5.0/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        /* ── Admin layout shell ─────────────────────────── */
        body.admin-layout {
            margin: 0; padding: 0;
            background: #f1f4f9;
            font-family: 'Sora', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* Main content — offset to the right of the fixed sidebar */
        .admin-content {
            margin-left: 280px; /* = --as-w in admin_navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .2s ease;
        }

        /* ── Topbar ─────────────────────────────────────── */
        .admin-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f3;
            padding: .7rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 200;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
            flex-shrink: 0;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: .55rem;
            color: #94a3b8;
            font-size: .78rem;
        }
        .topbar-left .topbar-sep   { color: #cbd5e1; }
        .topbar-left .topbar-page  { color: #1e293b; font-weight: 600; }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: .85rem;
        }
        /* Onduty chip */
        .topbar-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .72rem;
            font-weight: 600;
            padding: .28rem .7rem;
            border-radius: 999px;
            text-decoration: none;
            transition: background .2s;
            white-space: nowrap;
        }
        .topbar-chip.green { background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; }
        .topbar-chip.green:hover { background:#bbf7d0; color:#15803d; }
        .topbar-chip.red   { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; cursor:default; }
        .topbar-chip .dot  { width:7px; height:7px; border-radius:50%; background:currentColor; animation:pulse-chip 2s ease-in-out infinite; }
        @keyframes pulse-chip { 0%,100%{opacity:1} 50%{opacity:.4} }
        /* Clock */
        .topbar-clock {
            font-family: 'DM Mono', monospace;
            font-size: .72rem;
            color: #64748b;
            letter-spacing: .04em;
        }
        /* User quick-info */
        .topbar-avatar img {
            width:34px; height:34px;
            border-radius:50%;
            border:2px solid #d4af37;
            object-fit:cover;
        }
        .topbar-uname { font-size:.78rem; font-weight:600; color:#1e293b; line-height:1.2; }
        .topbar-urole { font-size:.67rem; color:#94a3b8; }

        /* ── Page body ──────────────────────────────────── */
        .admin-page-body { flex: 1; }

        /* ── Layout footer ──────────────────────────────── */
        .admin-layout-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f3;
            padding: .7rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .72rem;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .admin-layout-footer a { color:#64748b; text-decoration:none; transition:color .2s; }
        .admin-layout-footer a:hover { color:#d4af37; }
        .footer-brand-pill {
            display:inline-flex; align-items:center; gap:.35rem;
            font-size:.64rem; font-weight:700; letter-spacing:.08em;
            padding:.18rem .55rem; border-radius:4px;
            background:rgba(212,175,55,.1); border:1px solid rgba(212,175,55,.3); color:#d4af37;
        }
        .footer-socials { display:flex; align-items:center; gap:1rem; }
        .footer-socials a { font-size:1.05rem; }
        .footer-socials a:hover .fa-github   { color:#1f2328; }
        .footer-socials a:hover .fa-youtube  { color:#ff0033; }
        .footer-socials a:hover .fa-linkedin { color:#0077b5; }

        /* ── Responsive ─────────────────────────────────── */
        @media (max-width:991.98px) {
            .admin-content { margin-left: 0 !important; }
            .topbar-clock  { display: none !important; }
        }
        @media (max-width:575.98px) {
            .topbar-uname, .topbar-urole { display: none !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="admin-layout">

    {{-- ── Shared variables cho sidebar & topbar ──────── --}}
    @php
        $asEmp            = auth()->user()->employee ?? null;
        $asName           = $asEmp?->name_ingame ?? auth()->user()?->username ?? 'Admin';
        $asRole           = auth()->user()->role   ?? 'admin';
        $asRank           = $asEmp?->rank?->name_ranks ?? $asRole;
        $asAvatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($asName) . '&background=d4af37&color=0f1117';

        if ($asEmp?->discord_id) {
            $asAvatarSrc = $asEmp->discord_avatar;
        } elseif ($asEmp?->avatar) {
            $asAvatarSrc = asset('storage/' . $asEmp->avatar);
        } else {
            $asAvatarSrc = $asAvatarFallback;
        }

        $asOndutyCount = \App\Models\Attendance::where('status', 'Đang On-Duty')->count();
    @endphp

    {{-- ── Loading overlay (đồng bộ với layouts/app) ─── --}}
    <div id="loadingOverlay" class="loader-overlay" style="display:none;">
        <div class="lssd-loader-content">
            <div class="lssd-badge">
                <div class="star"></div>
                <div class="text">LSSD</div>
            </div>
            <div class="loading-text">Loading Los Santos Sheriff Department...</div>
            <div class="loader-clock">
                <span class="hour"></span>
                <span class="min"></span>
                <span class="circel"></span>
            </div>
        </div>
    </div>

    {{-- ── Sidebar (fixed left, 280px) ────────────────── --}}
    @include('partials.admin_navbar')

    {{-- ── Main content column ────────────────────────── --}}
    <div class="admin-content" id="adminContent">

        {{-- Top bar --}}
        <div class="admin-topbar">
            {{-- Breadcrumb --}}
            <div class="topbar-left">
                <i class="fa-solid fa-shield-halved" style="color:#d4af37;font-size:.88rem;"></i>
                <span>LSSD Quản Lý</span>
                <span class="topbar-sep">/</span>
                <span class="topbar-page">@yield('title', 'Dashboard')</span>
            </div>

            {{-- Right: status chip + clock + user --}}
            <div class="topbar-right">
                @if($asOndutyCount > 0)
                    <a href="{{ route('partials.onduty_live') }}" class="topbar-chip green">
                        <span class="dot"></span>
                        {{ $asOndutyCount }} Đang Onduty
                    </a>
                @else
                    <span class="topbar-chip red">
                        <span class="dot"></span>
                        Không có Onduty
                    </span>
                @endif

                <span class="topbar-clock d-none d-md-inline" id="adminClock"></span>

                <div class="d-flex align-items-center gap-2">
                    <div class="topbar-avatar">
                        <img src="{{ $asAvatarSrc }}" alt="Avatar"
                             onerror="this.onerror=null;this.src='{{ $asAvatarFallback }}'">
                    </div>
                    <div class="d-none d-sm-block">
                        <div class="topbar-uname">{{ $asName }}</div>
                        <div class="topbar-urole">{{ $asRole }}</div>
                    </div>
                </div>
            </div>
        </div>{{-- /.admin-topbar --}}

        {{-- Page content --}}
        <div class="admin-page-body">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer class="admin-layout-footer">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="footer-brand-pill">
                    <i class="fa-solid fa-shield-halved"></i> LSSD HRM
                </span>
                <span>© 2026 Designed by
                    <a href="https://jebsoon.netlify.app/" target="_blank">@jebsoon</a>
                    — v0.1
                </span>
            </div>
            <div class="footer-socials">
                <a href="https://github.com/songitcode"                   target="_blank" title="GitHub">   <i class="fa-brands fa-github"></i></a>
                <a href="https://www.youtube.com/@jason_ngy"              target="_blank" title="YouTube">  <i class="fa-brands fa-youtube"></i></a>
                <a href="https://www.linkedin.com/in/nguyenhoangson1606/" target="_blank" title="LinkedIn"> <i class="fa-brands fa-linkedin"></i></a>
            </div>
        </footer>

    </div>{{-- /.admin-content --}}

    {{-- Flash notifications (đồng bộ với layouts/app) --}}
    <div class="notifications">
        <span id="session-success" data-message="{{ session('success') }}"></span>
        <span id="session-warning" data-message="{{ session('warning') }}"></span>
        <span id="session-info"    data-message="{{ session('info') }}"></span>
        <span id="session-error"   data-message="{{ session('error') }}"></span>
    </div>

    <script src="{{ asset('assets/js/loading.js') }}"></script>
    <script src="{{ asset('assets/js/notification.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
        /* Live clock in topbar */
        (function () {
            const el = document.getElementById('adminClock');
            if (!el) return;
            const tick = () => {
                const n = new Date();
                const p = v => String(v).padStart(2, '0');
                el.textContent = `${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
            };
            tick(); setInterval(tick, 1000);
        })();
        // Loading khi submit form
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

    @stack('scripts')
</body>
</html>
