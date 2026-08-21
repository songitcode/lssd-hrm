<style>
    /* ═══════════════════════════════════════════════════════════════
   LSSD — ADMIN SIDEBAR
   Palette: Deep navy-black · Sheriff-star gold · Command green
   Signature: Gold gradient right-edge border + pulsing Live badges
   ═══════════════════════════════════════════════════════════════ */
    :root {
        --as-w: 280px;

        --as-bg: #4b3710;
        --as-surface: #463009;
        --as-card: #A7823E;
        --as-border: #C4A35A;
        --as-gold: #f5c54d;
        /*#F2D17C */
        --as-gold-light: #FFE39A;
        --as-gold-dark: #C49A42;
        --as-gold-dim: rgba(242, 209, 124, .18);
        --as-gold-hover: rgba(242, 209, 124, .28);
        --as-gold-glow: 0 0 16px rgba(242, 209, 124, .35);
        --as-text: #F8F4E8;
        --as-text-dim: #E0D3B0;
        --as-text-bright: #FFFFFF;
        --as-green: #22C55E;
        --as-red: #EF4444;
        --as-radius: 10px;
        --as-t: .25s ease;
    }

    body.admin-layout {
        margin: 0;
        padding: 0;
        background: #f1f4f9;
        overflow-x: hidden;
    }

    /* ── Sidebar ──────────────────────────────────────────────── */
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--as-w);
        height: 100vh;
        background: var(--as-bg);
        display: flex;
        flex-direction: column;
        z-index: 1040;
        overflow: hidden;
        transition: transform var(--as-t);
        box-shadow: 4px 0 32px rgba(0, 0, 0, .55);
    }

    /* Signature: gold gradient on the right edge */
    .admin-sidebar::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 1px;
        height: 100%;
        background: linear-gradient(to bottom, var(--as-gold) 0%, rgba(212, 175, 55, .25) 35%, transparent 70%);
        pointer-events: none;
    }

    /* ── Scrollable nav area ──────────────────────────────────── */
    .as-scroll-area {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: var(--as-border) transparent;
    }

    .as-scroll-area::-webkit-scrollbar {
        width: 4px;
    }

    .as-scroll-area::-webkit-scrollbar-thumb {
        background: var(--as-border);
        border-radius: 4px;
    }

    /* ── Brand ───────────────────────────────────────────────── */
    .as-brand {
        padding: 1.2rem 1.1rem 1rem;
        display: flex;
        align-items: center;
        gap: .8rem;
        flex-shrink: 0;
        border-bottom: 1px solid var(--as-border);
        background: linear-gradient(135deg, var(--as-surface) 0%, var(--as-bg) 100%);
    }

    .as-brand-logo {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        filter: drop-shadow(var(--as-gold-glow));
        transition: filter .35s;
    }

    .as-brand:hover .as-brand-logo {
        filter: drop-shadow(0 0 20px rgba(212, 175, 55, .65));
    }

    .as-brand-name {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--as-gold);
        line-height: 1.3;
        margin: 0 0 .1rem;
    }

    .as-brand-sub {
        font-size: .59rem;
        color: var(--as-text-dim);
        letter-spacing: .04em;
    }

    .as-admin-pill {
        margin-left: auto;
        flex-shrink: 0;
        font-size: .57rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: .22rem .6rem;
        border-radius: 4px;
        background: var(--as-gold-dim);
        border: 1px solid rgba(212, 175, 55, .4);
        color: var(--as-gold);
    }

    /* ── User card ────────────────────────────────────────────── */
    .as-user-card {
        padding: .9rem 1.1rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        background: var(--as-surface);
        border-bottom: 1px solid var(--as-border);
        flex-shrink: 0;
        text-decoration: none;
        transition: background var(--as-t);
    }

    .as-user-card:hover {
        background: var(--as-card);
    }

    .as-user-card img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid var(--as-gold);
        object-fit: cover;
        flex-shrink: 0;
    }

    .as-user-name {
        font-size: .8rem;
        font-weight: 600;
        color: var(--as-text-bright);
        margin: 0 0 .18rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .as-user-role-badge {
        display: inline-block;
        font-size: .6rem;
        font-weight: 700;
        text-transform: capitalize;
        letter-spacing: .04em;
        padding: .12rem .5rem;
        border-radius: 4px;
        background: var(--as-gold-dim);
        color: var(--as-gold);
        border: 1px solid #fff;
    }

    .as-online-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--as-green);
        margin-left: auto;
        flex-shrink: 0;
        box-shadow: 0 0 6px var(--as-green);
        animation: pulse-dot 2.5s ease-in-out infinite;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: .35
        }
    }

    /* ── Navigation ───────────────────────────────────────────── */
    .as-nav {
        padding: .6rem .65rem 1rem;
    }

    .as-section-label {
        font-size: .57rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--as-text-dim);
        padding: .9rem .65rem .3rem;
        margin: 0;
        user-select: none;
    }

    .as-link {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .52rem .75rem;
        border-radius: var(--as-radius);
        color: var(--as-text);
        font-size: .8rem;
        font-weight: 500;
        text-decoration: none;
        margin: .06rem 0;
        transition: background var(--as-t), color var(--as-t);
        cursor: pointer;
        user-select: none;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }

    .as-link .as-icon {
        width: 20px;
        text-align: center;
        font-size: .85rem;
        flex-shrink: 0;
        color: var(--as-text-dim);
        transition: color var(--as-t);
    }

    .as-link .as-label {
        flex: 1;
    }

    .as-link:hover {
        background: rgba(255, 255, 255, .04);
        color: var(--as-text-bright);
    }

    .as-link:hover .as-icon {
        color: var(--as-gold);
    }

    .as-link.active {
        background: var(--as-gold-dim);
        color: var(--as-gold);
        font-weight: 600;
        box-shadow: inset 2px 0 0 var(--as-gold);
    }

    .as-link.active .as-icon {
        color: var(--as-gold);
    }

    /* Badges */
    .as-badge-num {
        margin-left: auto;
        background: var(--as-red);
        color: #fff;
        font-size: .6rem;
        font-weight: 700;
        padding: .14rem .45rem;
        border-radius: 999px;
        min-width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    .as-badge-live {
        margin-left: auto;
        background: var(--as-green);
        color: #fff;
        font-size: .58rem;
        font-weight: 800;
        padding: .12rem .45rem;
        border-radius: 999px;
        letter-spacing: .06em;
        flex-shrink: 0;
        animation: pulse-live 2s ease-in-out infinite;
    }

    @keyframes pulse-live {

        0%,
        100% {
            opacity: 1;
            box-shadow: 0 0 6px var(--as-green)
        }

        50% {
            opacity: .6;
            box-shadow: none
        }
    }

    /* Chevron */
    .as-chevron {
        margin-left: auto;
        font-size: .7rem;
        color: var(--as-text-dim);
        flex-shrink: 0;
        transition: transform .3s ease, color var(--as-t);
    }

    .as-chevron.rotated {
        transform: rotate(180deg);
        color: var(--as-gold);
    }

    /* Sub-nav */
    .as-subnav {
        margin: .1rem 0 .15rem 1.05rem;
        border-left: 1.5px solid var(--as-border);
        overflow: hidden;
        max-height: 0;
        transition: max-height .35s ease;
    }

    .as-subnav.open {
        max-height: 280px;
    }

    .as-subnav .as-link {
        border-radius: 0 var(--as-radius) var(--as-radius) 0;
        font-size: .77rem;
        padding: .44rem .75rem;
        color: var(--as-text-dim);
    }

    .as-subnav .as-link:hover {
        color: var(--as-text);
    }

    .as-subnav .as-link.active {
        color: var(--as-gold);
        background: var(--as-gold-dim);
        box-shadow: inset 2px 0 0 var(--as-gold);
    }

    .as-subnav .as-link .as-icon {
        font-size: .72rem;
        width: 16px;
    }

    /* Divider */
    .as-divider {
        height: 1px;
        background: var(--as-border);
        margin: .6rem .65rem;
    }

    /* ── Footer / Logout ──────────────────────────────────────── */
    .as-footer {
        padding: .75rem .65rem;
        border-top: 1px solid var(--as-border);
        flex-shrink: 0;
        background: var(--as-bg);
    }

    .as-link.logout {
        color: #f87171;
    }

    .as-link.logout .as-icon {
        color: #f87171;
    }

    .as-link.logout:hover {
        background: rgba(239, 68, 68, .1);
        color: #ef4444;
    }

    .as-link.logout:hover .as-icon {
        color: #ef4444;
    }

    /* ── Mobile overlay + toggle ──────────────────────────────── */
    .admin-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .65);
        z-index: 1039;
        backdrop-filter: blur(2px);
    }

    .admin-overlay.show {
        display: block;
    }

    .as-mobile-btn {
        display: none;
        position: fixed;
        top: .8rem;
        left: .9rem;
        z-index: 1041;
        width: 40px;
        height: 40px;
        border-radius: var(--as-radius);
        background: var(--as-bg);
        border: 1px solid var(--as-border);
        color: var(--as-gold);
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        transition: background var(--as-t);
    }

    .as-mobile-btn:hover {
        background: var(--as-surface);
    }

    @media (max-width:991.98px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }

        .admin-sidebar.open {
            transform: translateX(0);
        }

        .as-mobile-btn {
            display: flex;
        }
    }
</style>

<!-- Mobile overlay -->
<div class="admin-overlay" id="adminOverlay"></div>
<!-- Mobile toggle -->
<button class="as-mobile-btn" id="asMobileBtn" aria-label="Mở menu">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Brand -->
    <div class="as-brand">
        <img class="as-brand-logo" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="LSSD">
        <div>
            <p class="as-brand-name">Los Santos Sheriff's Dept.</p>
            <span class="as-brand-sub">Trang quản lý LSSD</span>
        </div>
        <span class="as-admin-pill">Quản Lý</span>
    </div>

    <!-- User card (link to profile) -->
    <a href="{{ route('profile') }}" class="as-user-card" title="Xem hồ sơ">
        <img src="{{ $asAvatarSrc }}" alt="Avatar" onerror="this.onerror=null;this.src='{{ $asAvatarFallback }}'">
        <div style="min-width:0;">
            <div class="as-user-name">{{ $asName }}</div>
            <span class="as-user-role-badge">{{ $asRank }}</span>
            <span class="as-user-role-badge">{{ $asRole }}</span>
        </div>
        <span class="as-online-dot" title="Đang hoạt động"></span>
    </a>

    <!-- Scrollable nav -->
    <div class="as-scroll-area">
        <nav class="as-nav">

            <!-- Tổng Quan -->
            <p class="as-section-label">Tổng Quan</p>
            <a class="as-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="fa-solid fa-house as-icon"></i>
                <span class="as-label">Trang Chủ</span>
            </a>

            <!-- Nhân Sự -->
            <p class="as-section-label">Nhân Sự</p>
            <a class="as-link {{ request()->is('employees') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <i class="fa-solid fa-users as-icon"></i>
                <span class="as-label">Quản Lý Nhân Sự</span>
            </a>

            <!-- Báo Cáo (collapsible) -->
            <p class="as-section-label">Báo Cáo & Phân Tích</p>
            <button class="as-link {{ request()->is('reports*') ? 'active' : '' }}" id="asReportToggle" type="button">
                <i class="fa-solid fa-chart-pie as-icon"></i>
                <span class="as-label">Báo Cáo</span>
                <i class="fa-solid fa-chevron-down as-chevron {{ request()->is('reports*') ? 'rotated' : '' }}"
                    id="asReportChevron"></i>
            </button>
            <div class="as-subnav {{ request()->is('reports*') ? 'open' : '' }}" id="asReportSub">
                <a class="as-link {{ (request()->routeIs('reports.index') && !request()->is('reports/*')) ? 'active' : '' }}"
                    href="{{ route('reports.index') }}">
                    <i class="fa-solid fa-gauge-high as-icon"></i>
                    <span class="as-label">Tổng Quan</span>
                </a>
                <a class="as-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}"
                    href="{{ route('reports.attendance') }}">
                    <i class="fa-solid fa-clock as-icon"></i>
                    <span class="as-label">Chấm Công</span>
                </a>
                <a class="as-link {{ request()->routeIs('reports.payroll') ? 'active' : '' }}"
                    href="{{ route('reports.payroll') }}">
                    <i class="fa-solid fa-sack-dollar as-icon"></i>
                    <span class="as-label">Lương</span>
                </a>
                <a class="as-link {{ request()->routeIs('reports.employees') ? 'active' : '' }}"
                    href="{{ route('reports.employees') }}">
                    <i class="fa-solid fa-id-badge as-icon"></i>
                    <span class="as-label">Nhân Sự</span>
                </a>
            </div>

            <!-- Tài Chính -->
            <p class="as-section-label">Tài Chính</p>
            <a class="as-link {{ request()->is('salary-configs') ? 'active' : '' }}"
                href="{{ route('salary_configs.index') }}">
                <i class="fa-solid fa-scale-unbalanced as-icon"></i>
                <span class="as-label">Hệ Số Lương</span>
            </a>
            <a class="as-link {{ request()->is('payroll') ? 'active' : '' }}" href="{{ route('payroll.index') }}">
                <i class="fa-solid fa-sack-dollar as-icon"></i>
                <span class="as-label">Công / Lương</span>
            </a>

            <!-- Vận Hành -->
            <p class="as-section-label">Vận Hành</p>
            <a class="as-link {{ request()->routeIs('partials.ondutyList') ? 'active' : '' }}"
                href="{{ route('partials.ondutyList') }}">
                <i class="fa-solid fa-shield-halved as-icon"></i>
                <span class="as-label">Quản Lý Onduty</span>
                @if($asOndutyCount > 0)
                    <span class="as-badge-num">{{ $asOndutyCount }}</span>
                @endif
            </a>
            <a class="as-link {{ request()->routeIs('partials.onduty_live') ? 'active' : '' }}"
                href="{{ route('partials.onduty_live') }}">
                <i class="fa-solid fa-circle-dot as-icon" style="color:#22c55e;"></i>
                <span class="as-label">Real-Time Onduty</span>
                @if($asOndutyCount > 0)
                    <span class="as-badge-live">Live</span>
                @endif
            </a>

            <div class="as-divider"></div>

            <!-- Trang Thành Viên -->
            <p class="as-section-label">Trang Thành Viên</p>
            <a class="as-link {{ request()->is('attendance') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                <i class="fa-solid fa-clock as-icon"></i>
                <span class="as-label">Chấm Công</span>
            </a>
            <a class="as-link {{ request()->is('bao-lanh-toi-pham') ? 'active' : '' }}"
                href="{{ route('partials.criminal_bail') }}">
                <i class="fa-solid fa-gavel as-icon"></i>
                <span class="as-label">Bảo Lãnh Tội Phạm</span>
            </a>
            <a class="as-link {{ request()->is('ho-tro-xu-an') ? 'active' : '' }}"
                href="{{ route('partials.proc_records') }}">
                <i class="fa-solid fa-file-lines as-icon"></i>
                <span class="as-label">Hỗ Trợ Xử Án</span>
            </a>
            <a class="as-link {{ request()->is('ho-tro-truy-na') ? 'active' : '' }}"
                href="{{ route('partials.wanted_support') }}">
                <i class="fa-solid fa-magnifying-glass as-icon"></i>
                <span class="as-label">Hỗ Trợ Truy Nã</span>
            </a>
        </nav>
    </div>

    <!-- Footer / Logout -->
    <div class="as-footer">
        <a class="as-link logout" href="{{ route('logout') }}"
            onclick="event.preventDefault();document.getElementById('admin-logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket as-icon"></i>
            <span class="as-label">Đăng Xuất</span>
        </a>
        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* Reports sub-menu */
            const rToggle = document.getElementById('asReportToggle');
            const rSub = document.getElementById('asReportSub');
            const rChevron = document.getElementById('asReportChevron');
            if (rToggle) {
                rToggle.addEventListener('click', () => {
                    const open = rSub.classList.toggle('open');
                    rChevron.classList.toggle('rotated', open);
                });
            }

            /* Mobile sidebar */
            const mBtn = document.getElementById('asMobileBtn');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminOverlay');
            const open = () => { sidebar.classList.add('open'); overlay.classList.add('show'); };
            const close = () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); };
            if (mBtn) mBtn.addEventListener('click', open);
            if (overlay) overlay.addEventListener('click', close);
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