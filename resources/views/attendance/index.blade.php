@extends('layouts.app')

@section('title', 'Chấm Công — LSSD')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 (nếu chưa có) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== LIGHT THEME (mặc định) ===== */
        :root {
            --gold:          #b8860b;
            --gold-light:    #e0b354;
            --gold-dim:      #8b6914;
            --gold-month-header: #f0e4cc;
            --bg-page:       #f4f7fc;
            --card-bg:       #ffffff;
            --panel-bg:      #f9fafc;
            --border-light:  #d9e2ef;
            --hover-bg:      #eef3fa;
            --red-hot:       #e03535;
            --green-duty:    #2ecc71;
            --text-main:     #1e293b;
            --text-muted:    #5e718d;
            --text-dim:      #8a9cb0;
            --shadow-sm:     0 4px 12px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.05);
            --shadow-hover:  0 8px 20px rgba(0,0,0,0.06), 0 2px 6px rgba(0,0,0,0.05);
            --header-gradient: linear-gradient(135deg, #ffffff 0%, #f0f6ff 100%);
        }

        /* ===== DARK THEME ===== */
        [data-theme="dark"] {
            --gold:          #c9a84c;
            --gold-light:    #e4c97e;
            --gold-dim:      #7a5e24;
            --bg-page:       #0e1117;
            --card-bg:       #161b26;
            --panel-bg:      #1c2233;
            --border-light:  #2a3348;
            --hover-bg:      #222d45;
            --red-hot:       #e03535;
            --green-duty:    #2ecc71;
            --text-main:     #d8e0f0;
            --text-muted:    #7a8fad;
            --text-dim:      #4a5a78;
            --shadow-sm:     0 4px 12px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.2);
            --shadow-hover:  0 8px 20px rgba(0,0,0,0.4), 0 2px 6px rgba(0,0,0,0.2);
            --header-gradient: linear-gradient(135deg,#111827 0%,#1a2540 60%,#0f1622 100%);
        }

        body {
            background-color: var(--bg-page) !important;
            color: var(--text-main) !important;
            font-family: 'Source Sans 3', sans-serif !important;
            transition: background-color 0.2s, color 0.2s;
        }
        .atd-page { max-width: 1280px; margin: 0 auto; padding: 28px 20px 60px; }

        /* HIGHLIGHT ONDUTY */
       .row-ongoing {
            background: linear-gradient(90deg, rgba(0, 255, 30, 0.5), rgba(255,215,0,0.05));
            border-left: 3px solid gold;
        }

        @keyframes pulseRow {
            0% { background-color: rgba(255, 215, 0, 0.05); }
            50% { background-color: rgba(255, 215, 0, 0.15); }
            100% { background-color: rgba(255, 215, 0, 0.05); }
        }

        /* HEADER */
        .atd-header {
            position: relative; display: flex; align-items: center;
            justify-content: space-between; gap: 20px; padding: 28px 36px;
            margin-bottom: 28px;
            background: var(--header-gradient);
            border: 1px solid var(--border-light); border-top: 3px solid var(--gold);
            border-radius: 16px; overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .atd-header::before {
            content:''; position:absolute; inset:0;
            background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23b8860b' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events:none;
        }
        .atd-header-label {
            font-family:'Oswald',sans-serif; font-size:11px; font-weight:500;
            letter-spacing:4px; text-transform:uppercase; color:var(--gold-dim); margin-bottom:6px;
        }
        .atd-header-title {
            font-family:'Oswald',sans-serif; font-size:30px; font-weight:700;
            letter-spacing:2px; color:var(--text-main); margin:0; line-height:1;
        }
        .atd-header-title span { color:var(--gold); }

        /* Nút chuyển theme */
        .theme-toggle {
            background: transparent;
            border: 1px solid var(--border-light);
            border-radius: 40px;
            padding: 8px 12px;
            margin-left: 15px;
            cursor: pointer;
            color: var(--text-main);
            font-size: 18px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .theme-toggle:hover {
            background: var(--hover-bg);
            border-color: var(--gold-dim);
        }

        /* STAT CARDS */
        .atd-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
        @media(max-width:900px){.atd-stats{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:500px){.atd-stats{grid-template-columns:1fr;}}
        .stat-card {
            background:var(--card-bg); border:1px solid var(--border-light); border-radius:16px;
            padding:20px 22px; position:relative; overflow:hidden; transition:all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .stat-card:hover { border-color:var(--gold-dim); transform:translateY(-3px); box-shadow: var(--shadow-hover); }
        .stat-card::after {
            content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg,transparent,var(--gold-dim),transparent);
        }
        .stat-icon { font-size:20px; color:var(--gold-dim); margin-bottom:10px; }
        .stat-label { font-size:10px; font-weight:600; letter-spacing:2.5px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; }
        .stat-value { font-family:'Oswald',sans-serif; font-size:24px; font-weight:600; color:var(--text-main); letter-spacing:1px; }
        .stat-value.gold  { color:var(--gold-dim); }
        .stat-value.green { color:var(--green-duty); }

        /* CONTROL PANEL */
        .atd-control-panel { display:flex; align-items:stretch; gap:16px; margin-bottom:28px; flex-wrap:wrap; }
        .discord-status-box {
            flex:1; min-width:260px; background:var(--card-bg);
            border:1px solid var(--border-light); border-radius:16px; padding:18px 22px;
            box-shadow: var(--shadow-sm);
        }
        .discord-status-box .box-title {
            font-family:'Oswald',sans-serif; font-size:11px; letter-spacing:3px;
            text-transform:uppercase; color:var(--gold); margin-bottom:12px;
        }
        .status-row { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted); margin-bottom:6px; }
        .status-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
        .status-dot.online  { background:var(--green-duty); box-shadow:0 0 6px var(--green-duty); }
        .status-dot.offline { background:var(--text-dim); }
        .status-dot.warn    { background:#f0a500; box-shadow:0 0 6px #f0a500; }

        /* DUTY BUTTONS */
        .duty-btn-box { display:flex; flex-direction:column; align-items:flex-end; justify-content:center; gap:10px; flex-shrink:0; }
        .btn-on-duty,.btn-off-duty,.btn-locked,.btn-full {
            font-family:'Oswald',sans-serif; font-size:15px; font-weight:600;
            letter-spacing:3px; text-transform:uppercase; border:none; border-radius:40px;
            padding:14px 36px; cursor:pointer; transition:all .2s; display:flex; align-items:center; gap:10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .btn-on-duty { background:var(--gold); color:#fff; }
        .btn-on-duty:hover { background:var(--gold-light); transform:translateY(-2px); box-shadow:0 8px 18px rgba(184,134,11,0.2); }
        .btn-off-duty { background:var(--red-hot); color:#fff; }
        .btn-off-duty:hover { background:#ff4444; transform:translateY(-2px); box-shadow:0 8px 18px rgba(224,53,53,0.2); }
        .btn-locked { background:var(--panel-bg); color:var(--text-dim); border:1px solid var(--border-light); cursor:not-allowed; }
        .btn-full { background:transparent; color:var(--green-duty); border:1px solid var(--green-duty); cursor:default; font-size:12px; letter-spacing:2px; padding:10px 24px; }

        /* OVERTIME BOX */
        .overtime-box {
            background:var(--card-bg); border:1px solid var(--border-light);
            border-left:4px solid var(--gold-dim); border-radius:16px; padding:14px 20px;
            flex:1; min-width:260px; font-size:13px; box-shadow: var(--shadow-sm);
        }
        .overtime-box summary {
            cursor:pointer; color:var(--gold-dim); font-family:'Oswald',sans-serif;
            letter-spacing:1.5px; font-size:13px; user-select:none; list-style:none;
            display:flex; align-items:center; gap:8px;
        }
        .overtime-box summary::before { content:'▶'; font-size:10px; transition:transform .2s; }
        .overtime-box details[open] summary::before { transform:rotate(90deg); }
        .overtime-box ol { margin:12px 0 10px; padding-left:18px; color:var(--text-muted); line-height:2; }
        .overtime-box ol li { border-bottom:1px solid var(--border-light); }
        .badge-warn  { display:inline-block; background:rgba(240,165,0,.08); color:#b85e00; border:1px solid rgba(240,165,0,.3); border-radius:40px; padding:3px 10px; font-size:11px; letter-spacing:1px; margin-right:6px; }
        .badge-danger{ display:inline-block; background:rgba(224,53,53,.06); color:#c0392b; border:1px solid rgba(224,53,53,.2); border-radius:40px; padding:3px 10px; font-size:11px; letter-spacing:1px; }

        /* SECTION HEADING */
        .section-heading {
            font-family:'Oswald',sans-serif; font-size:13px; letter-spacing:4px;
            text-transform:uppercase; color:var(--gold-dim); padding-bottom:10px;
            border-bottom:1px solid var(--border-light); margin-bottom:16px;
            display:flex; align-items:center; gap:10px;
        }
        .section-heading::before { content:''; display:inline-block; width:4px; height:18px; background:var(--gold); border-radius:4px; }

        /* ATTENDANCE TABLE */
        .atd-table-wrap { background:var(--card-bg); border:1px solid var(--border-light); border-radius:16px; overflow:hidden; margin-bottom:28px; box-shadow: var(--shadow-sm); }
        .table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .atd-table { width:100%; border-collapse:collapse; font-size:13px; }
        .atd-table thead tr { background:var(--panel-bg); }
        .atd-table thead th {
            padding:13px 14px; text-align:center; font-family:'Oswald',sans-serif;
            font-size:11px; letter-spacing:2.5px; text-transform:uppercase;
            color:var(--gold-dim); border-bottom:2px solid var(--gold-dim); white-space:nowrap;
        }
        .atd-table tbody tr { border-bottom:1px solid var(--border-light); transition:background .15s; }
        .atd-table tbody tr:hover { background:var(--hover-bg); }
        .atd-table td { padding:11px 14px; text-align:center; color:var(--text-main); vertical-align:middle; }
        .atd-table td.td-id   { color:var(--text-dim); font-size:11px; }
        .atd-table td.td-name { font-weight:600; color:var(--text-main); }
        .atd-table td.td-wage { color:var(--green-duty); font-weight:600; font-family:'Oswald',sans-serif; letter-spacing:1px; }
        .atd-table td.td-time { color:var(--text-muted); font-size:12px; line-height:1.5; }
        .month-header td {
            background:var(--gold-month-header); font-family:'Oswald',sans-serif; font-size:12px;
            letter-spacing:3px; text-transform:uppercase; color:var(--text-muted);
            padding:9px 14px !important; text-align:center;
        }
        .day-total-row td {
            background:rgba(184,134,11,0.04); color:var(--gold-dim); font-size:12px;
            font-weight:600; padding:10px 14px !important; letter-spacing:.5px;
            border-top:1px solid rgba(184,134,11,0.2); border-bottom:2px solid var(--border-light);
        }

        /* STATUS BADGES */
        .badge-status { display:inline-flex; align-items:center; gap:5px; border-radius:40px; padding:4px 12px; font-size:11px; font-weight:600; letter-spacing:1px; white-space:nowrap; }
        .badge-done    { background:rgba(46,204,113,0.1); color:#1e7b4c; border:1px solid rgba(46,204,113,0.3); }
        .badge-onduty  { background:rgba(184,134,11,0.1); color:var(--gold-dim); border:1px solid rgba(184,134,11,0.3); animation:pulse-gold 2s infinite; }
        .badge-manager { background:rgba(224,53,53,0.08); color:#b91c1c; border:1px solid rgba(224,53,53,0.2); }
        .badge-auto    { background:rgba(46,204,113,0.06); color:#1e7b4c; border:1px solid rgba(46,204,113,0.2); }
        .badge-excess  { background:rgba(224,53,53,0.06); color:#b91c1c; border:1px solid rgba(224,53,53,0.15); }
        .badge-remain  { background:rgba(52,152,219,0.08); color:#1a5276; border:1px solid rgba(52,152,219,0.2); }
        .badge-default { background:rgba(94,113,141,0.08); color:var(--text-muted); border:1px solid var(--border-light); }
        @keyframes pulse-gold {
            0%,100% { box-shadow:0 0 0 0 rgba(184,134,11,0.3); }
            50%      { box-shadow:0 0 0 5px rgba(184,134,11,0); }
        }

        /* MONTHLY SUMMARY */
        .summary-table-wrap { background:var(--card-bg); border:1px solid var(--border-light); border-radius:16px; overflow:hidden; margin-bottom:28px; box-shadow: var(--shadow-sm); }
        .summary-table { width:100%; border-collapse:collapse; font-size:13px; }
        .summary-table thead tr { background:var(--panel-bg); }
        .summary-table thead th {
            padding:12px 20px; text-align:center; font-family:'Oswald',sans-serif;
            font-size:11px; letter-spacing:2px; text-transform:uppercase;
            color:var(--gold-dim); border-bottom:2px solid var(--gold-dim);
        }
        .summary-table tbody tr { border-bottom:1px solid var(--border-light); transition:background .15s; }
        .summary-table tbody tr:hover { background:var(--hover-bg); }
        .summary-table td { padding:13px 20px; text-align:center; color:var(--text-main); }
        .summary-table td:first-child { font-family:'Oswald',sans-serif; font-size:15px; letter-spacing:1px; color:var(--text-main); }
        .summary-table td:last-child   { color:var(--green-duty); font-weight:600; font-family:'Oswald',sans-serif; letter-spacing:1px; }

        /* PAGINATION */
        .pagination .page-link { background:var(--card-bg) !important; border-color:var(--border-light) !important; color:var(--text-muted) !important; }
        .pagination .page-item.active .page-link { background:var(--gold) !important; border-color:var(--gold) !important; color:#ffffff !important; }
        .pagination .page-link:hover { background:var(--hover-bg) !important; color:var(--gold) !important; }

        /* MISC */
        .btn-discord { display:inline-flex; align-items:center; gap:7px; background:#5865F2; color:#fff; border:none; border-radius:40px; padding:6px 16px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; transition:background .2s; }
        .btn-discord:hover { background:#4752c4; color:#fff; text-decoration:none; }
        #timer-main { font-family:'Oswald',sans-serif; font-size:16px; font-weight:700; color:var(--red-hot); letter-spacing:3px; }
        .test { display:none; }

        /* Header right group */
        .header-right-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .atd-page {
                padding: 16px 12px 40px;
            }

            /* Header */
            .atd-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px 20px;
                gap: 16px;
            }

            .header-right-group {
                width: 100%;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 12px;
            }

            .theme-toggle {
                margin-left: 0;
                padding: 6px 12px;
                font-size: 14px;
            }

            .duty-btn-box {
                align-items: flex-start;
                width: 100%;
            }

            .btn-on-duty, .btn-off-duty, .btn-locked, .btn-full {
                padding: 10px 20px;
                font-size: 13px;
                letter-spacing: 2px;
                width: 100%;
                justify-content: center;
            }

            /* Stats */
            .atd-stats {
                gap: 12px;
            }

            .stat-card {
                padding: 16px 16px;
            }

            .stat-value {
                font-size: 20px;
            }

            /* Control panel */
            .atd-control-panel {
                flex-direction: column;
                gap: 12px;
            }

            .discord-status-box,
            .overtime-box {
                min-width: unset;
                width: 100%;
            }

            /* Table */
            .atd-table {
                font-size: 11px;
            }

            .atd-table thead th {
                padding: 10px 8px;
                font-size: 9px;
                letter-spacing: 1.5px;
            }

            .atd-table td {
                padding: 8px 6px;
            }

            .badge-status {
                padding: 2px 8px;
                font-size: 9px;
                white-space: normal;
                line-height: 1.3;
            }

            /* Section heading */
            .section-heading {
                font-size: 11px;
                letter-spacing: 2px;
            }

            /* Summary table */
            .summary-table {
                font-size: 12px;
            }

            .summary-table td {
                padding: 10px 12px;
            }
        }

        @media (max-width: 480px) {
            .atd-header-title {
                font-size: 24px;
            }

            .atd-header-label {
                font-size: 9px;
                letter-spacing: 3px;
            }

            .theme-toggle span {
                display: none; /* Chỉ hiện icon trên mobile rất nhỏ */
            }

            .theme-toggle {
                padding: 8px;
                border-radius: 50%;
            }

            .stat-card {
                padding: 14px 12px;
            }

            .stat-label {
                font-size: 8px;
                letter-spacing: 1.5px;
            }

            .stat-value {
                font-size: 18px;
            }

            .btn-discord {
                padding: 4px 10px;
                font-size: 11px;
            }

            /* Ẩn bớt text trong bảng nếu cần */
            .atd-table td.td-time small {
                display: block;
                font-size: 9px;
            }

            .pagination .page-link {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $isFull = $totalTodayDuration >= $maxHourPerDay;
        $maintenance = false;
    @endphp
    @if($maintenance)
       @include('maintenance_2')
    @else
    {{-- Toàn bộ giao diện chấm công hiện tại --}}
    <div class="atd-page">

        {{-- ═══ HEADER ═══ --}}
        <div class="atd-header">
            <div>
                <div class="atd-header-label">Los Santos Sheriff's Department</div>
                <h1 class="atd-header-title">CHẤM CÔNG<span></span></h1>
            </div>
            <div class="header-right-group">
                {{-- Nút chuyển đổi theme --}}
                <button class="theme-toggle" id="themeToggle" aria-label="Chuyển giao diện tối/sáng">
                    <i class="fas fa-moon" id="themeIcon"></i>
                    <span id="themeText">Dark</span>
                </button>
                <div class="duty-btn-box">
                    <form method="POST" action="{{ route('attendance.check') }}">
                        @csrf
                        @if ($lanyardUnavailable)
                            <span class="btn-locked">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                &nbsp; Không Kiểm Tra Được Game
                            </span>

                        @elseif (!$isPlayingGame)
                            <span class="btn-locked">
                                <i class="fa-solid fa-lock"></i>
                                &nbsp; Chưa Vào Game
                            </span>

                        @elseif ($gameName === 'GTA5VN')
                            @if ($isFull)
                                <span class="btn-full">
                                    <i class="fa-solid fa-circle-check"></i>
                                    &nbsp; Đủ Giờ Hôm Nay
                                </span>
                            @else
                                <button
                                    type="submit"
                                    name="action"
                                    value="start"
                                    class="btn-on-duty {{ $ongoing ? 'd-none' : '' }}"
                                >
                                    <i class="fa-solid fa-circle-play"></i>
                                    On-Duty
                                </button>

                                <button
                                    type="submit"
                                    name="action"
                                    value="stop"
                                    class="btn-off-duty {{ !$ongoing ? 'd-none' : '' }}"
                                >
                                    <i class="fa-solid fa-circle-stop"></i>
                                    Off-Duty
                                </button>
                            @endif

                        @else
                            <span class="btn-locked">
                                <i class="fa-solid fa-lock"></i>
                                &nbsp; TẠM KHÓA
                            </span>
                        @endif
                    </form>
                    {{-- TEST (ẩn) --}}
                    <form method="POST" action="{{ route('attendance.check') }}" class="_test">
                        @csrf
                        <button type="submit" name="action" value="start"
                            class="btn-on-duty {{ $isFull || $ongoing ? 'd-none' : '' }}">
                            <i class="fa-solid fa-circle-play"></i> On-Duty
                        </button>
                        <button type="submit" name="action" value="stop"
                            class="btn-off-duty {{ $isFull || !$ongoing ? 'd-none' : '' }}">
                            <i class="fa-solid fa-circle-stop"></i> Off-Duty
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══ STAT CARDS ═══ --}}
        <div class="atd-stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-id-badge"></i></div>
                <div class="stat-label">Chức Vụ / Quân Hàm</div>
                <div class="stat-value" style="font-size:16px;line-height:1.4;">
                    {{ auth()->user()->position->name_positions ?? '-' }}<br>
                    <span style="color:var(--text-muted);font-size:13px;">{{ auth()->user()->employee->rank->name_ranks ?? '-' }}</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-scale-unbalanced"></i></div>
                <div class="stat-label">Hệ Số Lương / Giờ Tối Đa</div>
                <div class="stat-value">{{ number_format($heSoLuong ?? 0) }}<span style="font-size:13px;color:var(--text-muted);">$/h</span></div>
                <div style="font-size:11px;color:var(--text-muted);">Tối đa {{ number_format($maxHourPerDay ?? 0, 2) }}h/ngày</div>
            </div>
             <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="stat-label">Trực Tiếp Tháng {{ now()->format('m') }}</div>
                <div class="stat-value gold">{{ number_format($monthlyTotal) }}$</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                <div class="stat-label">Tổng Sự Nghiệp</div>
                <div class="stat-value green">{{ number_format($totalLuong) }}$</div>
            </div>
        </div>

        {{-- ═══ CONTROL PANEL ═══ --}}
        <div class="atd-control-panel">
            <div class="discord-status-box">
                <div class="box-title"><i class="fab fa-discord"></i>&nbsp; Trạng Thái Discord / Game</div>
                @if ($discordId === null)
                    <div class="status-row">
                        <span class="status-dot warn"></span>

                        Chưa liên kết Discord —

                        <a href="{{ route('discord.connect') }}"
                        class="btn-discord ms-2">
                            <i class="fab fa-discord"></i>
                            Liên Kết Ngay
                        </a>
                    </div>
                @elseif ($lanyardUnavailable)
                    <div class="status-row">
                        <span class="status-dot warn"></span>
                        <span>
                            Máy chủ trạng thái Discord đang tạm thời không phản hồi.
                            Vui lòng tải lại trang sau.
                        </span>
                    </div>
                @elseif ($isPlayingGame)
                    <div class="status-row">
                        <span class="status-dot online"></span>
                        <b style="color:var(--text-main)">
                            {{ $gameName }}
                        </b>
                        @if ($gameDetails)
                            &nbsp;/&nbsp;
                            <span>{{ $gameDetails }}</span>
                        @endif
                        @if ($gameState)
                            &nbsp;/&nbsp;
                            <span>{{ $gameState }}</span>
                        @endif
                    </div>
                    @if ($gameName !== 'GTA5VN')
                        <div class="status-row">
                            <span class="status-dot warn"></span>
                            Hoạt động
                            <b style="color:#f0a500">
                                {{ $gameName }}
                            </b>
                            không phải GTA5VN — Chấm công bị khoá
                        </div>
                    @endif
                @else
                    <div class="status-row">
                        <span class="status-dot offline"></span>
                        Chưa phát hiện hoạt động game.
                        Vui lòng bật Activity Status trên Discord và tham gia:
                    </div>
                    <div style="margin-top:8px;">
                        <a href="https://discord.gg/JeSrQWvTUy"
                        class="btn-discord"
                        id="discord-link"
                        target="_blank"
                        rel="noopener noreferrer">
                            <i class="fab fa-discord"></i>
                            Tham Gia Lanyard
                        </a>
                    </div>
                @endif
            </div>

            <div class="overtime-box">
                <details>
                    <summary>Quy Định Trừ Lương Quá Giờ</summary>
                    <ol>
                        <li>Tổng giờ ≥ 2h vượt → Trừ <b style="color:#f0a500">20%</b> lương ca đó</li>
                        <li>Tổng giờ ≥ 3h vượt → Trừ <b style="color:#e03535">50%</b> lương ca đó</li>
                        <li>Tổng giờ ≥ 6h vượt → Trừ <b style="color:#c0392b">90%</b> lương ca đó</li>
                    </ol>
                    <div style="margin-top:6px;">
                        <span class="badge-warn">Lương ca = giờ × hệ số lương</span>
                        <span class="badge-danger">Tiền trừ = Lương ca × % / 100</span>
                    </div>
                </details>
            </div>
        </div>

        {{-- ═══ ATTENDANCE TABLE ═══ --}}
        <div class="section-heading">Lịch Sử Chấm Công Chi Tiết</div>
        <div class="atd-table-wrap">
            <div class="table-scroll">
                <table class="atd-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Ngày</th>
                            <th>On-Duty</th>
                            <th>Off-Duty</th>
                            <th>Timer</th>
                            <th>Giờ</th>
                            <th>Lương</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $summariesByMonth = collect($dailySummaries)->groupBy(function ($summary) {
                                return \Carbon\Carbon::parse($summary['date'])->format('m/Y');
                            });
                        @endphp

                        @foreach ($summariesByMonth as $month => $monthSummaries)
                            <tr class="month-header">
                                <td colspan="9">
                                    <i class="fa-solid fa-calendar-days" style="color:var(--gold-dim);margin-right:6px;"></i>
                                    Bảng Chấm Công Tháng {{ $month }}
                                </td>
                            </tr>

                            @foreach ($monthSummaries as $summary)
                                @foreach ($summary['attendances'] as $att)
                                    <tr class="{{ (isset($ongoing) && $ongoing->id == $att->id && is_null($att->check_out)) ? 'row-ongoing' : '' }}">
                                        <td class="td-id">{{ $att->id }}</td>
                                        <td class="td-name">{{ $att->user->employee->name_ingame ?? $att->user->username }}</td>
                                        <td class="td-time">{{ date_format($att->date, 'd/m/Y') }}</td>
                                        <td class="td-time">
                                            {{ $att->check_in->format('H:i:s') }}<br>
                                            <small>{{ $att->check_in->format('d/m') }}</small>
                                        </td>
                                        <td class="td-time">
                                            @if($att->check_out)
                                                {{ $att->check_out->format('H:i:s') }}<br>
                                                <small>{{ $att->check_out->format('d/m') }}</small>
                                            @else
                                                <span style="color:var(--gold);font-size:11px;letter-spacing:1px;">Đang làm việc...</span>
                                            @endif
                                        </td>
                                        <td style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:600;color:var(--gold-dim);letter-spacing:2px;">
                                            @if(isset($ongoing) && $ongoing->id == $att->id && is_null($att->check_out))
                                                <div id="timer-main">00:00:00</div>
                                            @else
                                                <div class="timer-static">
                                                    {{ $att->check_out ? \Carbon\Carbon::parse($att->check_in)->diff(\Carbon\Carbon::parse($att->check_out))->format('%H:%I:%S') : '00:00:00' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ number_format($att->duration, 2) }}h</td>
                                        <td class="td-wage">{{ number_format($att->wage, 0) }}$</td>
                                        <td>
                                            @if ($att->status == 'Hoàn thành' || $att->status == 'Đang On-Duty')
                                                <span class="badge-status badge-done">
                                                    <i class="fa-solid fa-circle-check" style="font-size:9px;"></i> {{ $att->status }}
                                                </span>
                                            @elseif (Str::contains($att->status, 'Quản Lý'))
                                                <span class="badge-status badge-manager">
                                                    <i class="fa-solid fa-triangle-exclamation" style="font-size:9px;"></i> {{ $att->status }}
                                                </span>
                                            @elseif (Str::contains($att->status, 'Tự Động'))
                                                <span class="badge-status badge-auto">
                                                    <i class="fa-solid fa-robot" style="font-size:9px;"></i> {{ $att->status }}
                                                </span>
                                            @elseif (Str::contains($att->status, 'Dư'))
                                                <span class="badge-status badge-excess">
                                                    <i class="fa-solid fa-clock-rotate-left" style="font-size:9px;"></i> {{ $att->status }}
                                                </span>
                                            @elseif (Str::startsWith($att->status, 'Còn'))
                                                <span class="badge-status badge-remain">
                                                    <i class="fa-solid fa-hourglass-half" style="font-size:9px;"></i> {{ $att->status }}
                                                </span>
                                            @else
                                                <span class="badge-status badge-default">{{ $att->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                <tr class="day-total-row">
                                    <td colspan="5" style="text-align:left;padding-left:20px;">
                                        <i class="fa-solid fa-layer-group" style="margin-right:6px;"></i>
                                        Tổng Ngày {{ \Carbon\Carbon::parse($summary['date'])->format('d/m/Y') }}
                                    </td>
                                    <td colspan="3" style="color:var(--green-duty);">
                                        Lương: {{ number_format($summary['total_wage']) }}$
                                    </td>
                                    <td style="color:var(--gold-dim);">
                                        {{ number_format($summary['total_duration'], 2) }}h / {{ $maxHourPerDay }}h
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3 mb-4">
            {{ $attendancesPaginated->links() }}
        </div>

        {{-- ═══ MONTHLY SUMMARY ═══ --}}
        @if ($monthlySummaries->isNotEmpty())

            <div class="section-heading">
                Lịch Sử Tổng Kết Kỳ Lương
            </div>

            <div class="summary-table-wrap">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Kỳ Lương</th>
                            <th>Tổng Giờ</th>
                            <th>Tổng Lương</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($monthlySummaries as $summary)
                            <tr>

                                @if($summary->period_type === 'biweekly' && $summary->period_start)

                                    <td>
                                        {{ \Carbon\Carbon::parse($summary->period_start)->format('d/m') }}
                                        -
                                        {{ $summary->period_end
                                            ? \Carbon\Carbon::parse($summary->period_end)->format('d/m/Y')
                                            : '?' }}
                                    </td>

                                @else

                                    <td>
                                        {{ str_pad($summary->month,2,'0',STR_PAD_LEFT) }}/{{ $summary->year }}
                                    </td>

                                @endif

                                <td style="color:var(--text-muted);">
                                    {{ number_format($summary->total_hours,2) }}h
                                </td>

                                <td>
                                    {{ number_format($summary->total_wage) }}$
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @endif

    </div>
    @endif

@endsection

<script>
    const CHECK_IN_TIME = {{ $ongoing ? strtotime($ongoing->check_in) : 'null' }};
</script>

@push('scripts')
    <script>
        // Timer logic
        let timerInterval = null;
        const timerEl = document.getElementById('timer-main');

        function formatHhMmSs(seconds) {
            const hrs  = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            return `${hrs}:${mins}:${secs}`;
        }

        function updateTimerDisplay(seconds) {
            if (!timerEl) return;
            timerEl.textContent = formatHhMmSs(seconds);
        }

        function startServerTimer() {
            if (typeof CHECK_IN_TIME === 'undefined' || CHECK_IN_TIME === null) return;
            const now0 = Math.floor(Date.now() / 1000);
            let elapsed0 = now0 - CHECK_IN_TIME;
            if (elapsed0 < 0) elapsed0 = 0;
            updateTimerDisplay(elapsed0);
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                const now = Math.floor(Date.now() / 1000);
                let elapsed = now - CHECK_IN_TIME;
                if (!Number.isFinite(elapsed) || elapsed < 0) elapsed = 0;
                updateTimerDisplay(elapsed);
            }, 1000);
        }

        window.addEventListener('load', function () {
            if (timerEl && typeof CHECK_IN_TIME !== 'undefined' && CHECK_IN_TIME !== null) {
                startServerTimer();
            }
        });

        window.addEventListener('beforeunload', () => {
            if (timerInterval) clearInterval(timerInterval);
        });

        // Theme Toggle
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const htmlElement = document.documentElement;

            // Lấy theme đã lưu hoặc mặc định light
            const savedTheme = localStorage.getItem('lssd_theme') || 'light';
            htmlElement.setAttribute('data-theme', savedTheme);
            updateToggleUI(savedTheme);

            themeToggle.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('lssd_theme', newTheme);
                updateToggleUI(newTheme);
            });

            function updateToggleUI(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                    themeText.textContent = 'Sáng';
                } else {
                    themeIcon.className = 'fas fa-moon';
                    themeText.textContent = 'Tối';
                }
            }
        })();
    </script>
@endpush