@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        <style>
            :root {
                --sc-bg:           #f4f6fb;
                --sc-surface:      #ffffff;
                --sc-surface-2:    #f9fafd;
                --sc-border:       #e4e8f0;
                --sc-border-hover: #c9d1e0;
                --sc-blue:         #2563eb;
                --sc-blue-light:   #eff4ff;
                --sc-blue-dim:     #dbeafe;
                --sc-amber:        #d97706;
                --sc-amber-light:  #fffbeb;
                --sc-amber-dim:    #fef3c7;
                --sc-green:        #059669;
                --sc-green-light:  #ecfdf5;
                --sc-red:          #dc2626;
                --sc-red-light:    #fef2f2;
                --sc-text-1:       #0f172a;
                --sc-text-2:       #475569;
                --sc-text-3:       #94a3b8;
                --sc-shadow-sm:    0 1px 4px rgba(15,23,42,0.06);
                --sc-shadow-md:    0 4px 16px rgba(15,23,42,0.08);
                --sc-r-sm:         6px;
                --sc-r-md:         10px;
                --sc-r-lg:         14px;
                --sc-r-xl:         20px;
            }

            /* ── PAGE WRAPPER ────────────────────────────────── */
            .sc-page {
                min-height: 100vh;
                padding: 2.25rem 1.5rem 3rem;
                font-family: 'Sora', sans-serif;
                color: var(--sc-text-1);
            }
            .sc-wrap {
                /* max-width: 1060px;
                margin: 0 auto; */
            }

            /* ── PAGE HEADER ─────────────────────────────────── */
            .sc-header {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 2rem;
                animation: sc-up 0.45s ease both;
            }
            .sc-header-icon {
                width: 48px; height: 48px;
                background: var(--sc-blue-light);
                border: 1.5px solid var(--sc-blue-dim);
                border-radius: var(--sc-r-md);
                display: flex; align-items: center; justify-content: center;
                font-size: 1.3rem;
                flex-shrink: 0;
                box-shadow: var(--sc-shadow-sm);
            }
            .sc-header-text h1 {
                margin: 0 0 0.2rem;
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--sc-text-1);
                letter-spacing: -0.02em;
                line-height: 1.2;
            }
            .sc-header-text p {
                margin: 0;
                font-size: 0.85rem;
                color: var(--sc-text-2);
            }

            /* ── CARD ────────────────────────────────────────── */
            .sc-card {
                background: var(--sc-surface);
                border: 1px solid var(--sc-border);
                border-radius: var(--sc-r-xl);
                padding: 1.75rem 2rem;
                box-shadow: var(--sc-shadow-md);
                margin-bottom: 1.5rem;
                animation: sc-up 0.45s ease both;
            }
            .sc-section-label {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--sc-blue);
                margin-bottom: 1.1rem;
            }
            .sc-section-label::before {
                content: '';
                display: inline-block;
                width: 3px; height: 13px;
                background: var(--sc-blue);
                border-radius: 2px;
            }

            /* ── NO AUTH ─────────────────────────────────────── */
            .sc-no-auth {
                background: var(--sc-red-light);
                border: 1px solid #fecaca;
                border-radius: var(--sc-r-lg);
                padding: 1rem 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
                font-size: 0.88rem;
                color: var(--sc-red);
                animation: sc-up 0.45s ease both;
            }

            /* ── FORM LAYOUT ─────────────────────────────────── */
            .sc-row {
                display: grid;
                gap: 1rem;
                align-items: end;
            }
            .sc-row--3 { grid-template-columns: 1.4fr 1fr auto; }
            .sc-row--2 { grid-template-columns: 1fr auto; }

            .sc-field { display: flex; flex-direction: column; gap: 0.35rem; }
            .sc-label {
                font-size: 0.74rem;
                font-weight: 600;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: var(--sc-text-2);
            }

            /* ── INPUTS ──────────────────────────────────────── */
            .sc-input,
            .sc-select {
                appearance: none;
                -webkit-appearance: none;
                background: var(--sc-surface-2);
                border: 1.5px solid var(--sc-border);
                border-radius: var(--sc-r-md);
                color: var(--sc-text-1);
                padding: 0.62rem 0.9rem;
                font-family: 'Sora', sans-serif;
                font-size: 0.88rem;
                width: 100%;
                outline: none;
                transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            }
            .sc-select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2394a3b8' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.85rem center;
                padding-right: 2.2rem;
            }
            .sc-input:focus,
            .sc-select:focus {
                border-color: var(--sc-blue);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            }
            .sc-input::placeholder { color: var(--sc-text-3); }

            /* ── BUTTONS ─────────────────────────────────────── */
            .sc-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.65rem 1.3rem;
                border-radius: var(--sc-r-md);
                font-family: 'Sora', sans-serif;
                font-size: 0.85rem;
                font-weight: 600;
                cursor: pointer;
                border: none;
                white-space: nowrap;
                transition: all 0.18s;
                line-height: 1;
            }
            .sc-btn--blue {
                background: var(--sc-blue);
                color: #fff;
                box-shadow: 0 2px 8px rgba(37,99,235,0.25);
            }
            .sc-btn--blue:hover {
                background: #1d4ed8;
                box-shadow: 0 4px 14px rgba(37,99,235,0.35);
                transform: translateY(-1px);
            }
            .sc-btn--amber {
                background: var(--sc-amber-light);
                color: var(--sc-amber);
                border: 1.5px solid var(--sc-amber-dim);
            }
            .sc-btn--amber:hover {
                background: var(--sc-amber-dim);
                box-shadow: 0 4px 12px rgba(217,119,6,0.18);
                transform: translateY(-1px);
            }
            .sc-btn:active { transform: translateY(0) !important; }

            /* ── DIVIDER ─────────────────────────────────────── */
            .sc-divider {
                height: 1px;
                background: var(--sc-border);
                margin: 1.5rem 0;
            }

            /* ── TABLE CARD ──────────────────────────────────── */
            .sc-table-card {
                background: var(--sc-surface);
                border: 1px solid var(--sc-border);
                border-radius: var(--sc-r-xl);
                box-shadow: var(--sc-shadow-md);
                overflow: hidden;
                animation: sc-up 0.5s ease 0.08s both;
            }
            .sc-table-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1.75rem;
                border-bottom: 1px solid var(--sc-border);
                background: var(--sc-surface-2);
            }
            .sc-table-title {
                font-size: 0.84rem;
                font-weight: 600;
                color: var(--sc-text-2);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .sc-count-badge {
                background: var(--sc-blue-light);
                color: var(--sc-blue);
                border: 1px solid var(--sc-blue-dim);
                font-size: 0.7rem;
                font-weight: 700;
                padding: 0.18rem 0.55rem;
                border-radius: 99px;
            }

            /* ── TABLE ───────────────────────────────────────── */
            .sc-tbl {
                width: 100%;
                border-collapse: collapse;
            }
            .sc-tbl thead { background: var(--sc-surface-2); }
            .sc-tbl thead th {
                padding: 0.7rem 1.5rem;
                font-size: 0.69rem;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--sc-text-3);
                text-align: center;
                border-bottom: 1px solid var(--sc-border);
            }
            .sc-tbl tbody tr {
                border-bottom: 1px solid var(--sc-border);
                transition: background 0.12s;
            }
            .sc-tbl tbody tr:last-child { border-bottom: none; }
            .sc-tbl tbody tr:hover { background: #f5f8ff; }
            .sc-tbl tbody td {
                padding: 0.95rem 1.5rem;
                font-size: 0.88rem;
                vertical-align: middle;
            }

            /* ── CELL STYLES ─────────────────────────────────── */
            .sc-td-rank { font-weight: 600; color: var(--sc-text-1); font-size: 0.92rem; }
            .sc-td-rate {
                font-family: 'JetBrains Mono', monospace;
                font-size: 0.9rem;
                font-weight: 500;
                color: var(--sc-blue);
            }
            .sc-pill-hours {
                display: inline-flex;
                align-items: center;
                gap: 0.28rem;
                background: var(--sc-green-light);
                border: 1px solid #a7f3d0;
                color: var(--sc-green);
                font-size: 0.78rem;
                font-weight: 600;
                padding: 0.22rem 0.6rem;
                border-radius: 99px;
            }
            .sc-td-user { color: var(--sc-text-2); font-size: 0.84rem; }
            .sc-td-time {
                font-family: 'JetBrains Mono', monospace;
                font-size: 0.77rem;
                color: var(--sc-text-3);
            }

            /* ── ANIMATION ───────────────────────────────────── */
            @keyframes sc-up {
                from { opacity: 0; transform: translateY(12px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            /* ── RESPONSIVE ──────────────────────────────────── */
            @media (max-width: 720px) {
                .sc-row--3, .sc-row--2 { grid-template-columns: 1fr; }
                .sc-card { padding: 1.25rem; }
                .sc-tbl thead th, .sc-tbl tbody td { padding: 0.75rem 1rem; }
            }
        </style>
@endpush

@section('content')
    <div class="sc-page mt-5">
    <div class="sc-wrap container">

        {{-- ── FORM PANEL ── --}}
        @if (in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'admin']))
        {{-- ── HEADER ── --}}
            <div class="sc-header">
                <div class="sc-header-icon">⚙️</div>
                <div class="sc-header-text">
                    <h1>Quản lý hệ số lương</h1>
                    <p>Cấu hình mức lương theo quân hàm và thời gian làm việc toàn hệ thống</p>
                </div>
            </div>
            <div class="sc-card">

                {{-- Form 1: Hệ số lương --}}
                <div class="sc-section-label">Cập nhật hệ số lương</div>
                <form method="POST" action="{{ route('salary_configs.store') }}" class="sc-form-salary">
                    @csrf
                    <div class="sc-row sc-row--3">
                        <div class="sc-field">
                            <label class="sc-label">Quân hàm</label>
                            <select name="rank_id" class="sc-select" required>
                                <option value="">--- Chọn quân hàm ---</option>
                                @foreach($ranks as $rank)
                                    <option value="{{ $rank->id }}">{{ $rank->name_ranks }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sc-field">
                            <label class="sc-label">Lương / Giờ ($)</label>
                            <input type="text" name="hourly_rate_display" id="hourly_rate_display"
                                   class="sc-input" placeholder="Nhập mức lương..." required>
                            <input type="hidden" name="hourly_rate" id="hourly_rate" max="7">
                        </div>

                        <div class="sc-field">
                            <label class="sc-label" aria-hidden="true" style="opacity:0">‎</label>
                            <button type="submit" class="sc-btn sc-btn--blue">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Cập nhật hệ số
                            </button>
                        </div>
                    </div>
                </form>

                <div class="sc-divider"></div>

                {{-- Form 2: Giờ làm tối đa --}}
                <div class="sc-section-label">Thời gian làm việc</div>
                <form method="POST" action="{{ route('salary_configs.updateGlobalHours') }}" class="sc-form-edit-time">
                    @csrf
                    @method('PUT')
                    <div class="sc-row sc-row--2">
                        <div class="sc-field">
                            <label class="sc-label" for="max_hours_per_day">Giờ làm tối đa / ngày (toàn hệ thống)</label>
                            <input type="number" step="0.1" min="0" max="24"
                                   name="max_hours_per_day" id="max_hours_per_day"
                                   class="sc-input"
                                   value="{{ $configs->first()?->max_hours_per_day ?? 3 }}" required>
                        </div>
                        <div class="sc-field">
                            <label class="sc-label" aria-hidden="true" style="opacity:0">‎</label>
                            <button type="submit" class="sc-btn sc-btn--amber">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Cập nhật giờ làm
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @else
            <div class="sc-no-auth">
                <span>🔒</span>
                Bạn không có thẩm quyền để cập nhật hệ số lương.
            </div>
        @endif

        {{-- ── TABLE ── --}}
        <div class="sc-table-card">
            <div class="sc-table-bar">
                <span class="sc-table-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    Danh sách hệ số lương
                </span>
                <span class="sc-count-badge">{{ $configs->count() }} quân hàm</span>
            </div>

            <div style="overflow-x:auto;">
                <table class="sc-tbl">
                    <thead>
                        <tr>
                            <th>Quân hàm</th>
                            <th>Lương / Giờ</th>
                            <th>Giờ tối đa</th>
                            <th>Người cập nhật</th>
                            <th>Thời gian thay đổi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($configs as $cfg)
                            <tr>
                                <td class="sc-td-rank">{{ $cfg->rank->name_ranks }}</td>
                                <td class="sc-td-rate">{{ number_format($cfg->hourly_rate) }} $</td>
                                <td>
                                    <span class="sc-pill-hours">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $cfg->max_hours_per_day }}h
                                    </span>
                                </td>
                                <td class="sc-td-user">{{ $cfg->updatedBy?->username ?? 'Không rõ' }}</td>
                                <td class="sc-td-time">{{ $cfg->updated_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // ── Format số với dấu phẩy ──────────────────────
            const displayInput = document.getElementById('hourly_rate_display');
            const hiddenInput  = document.getElementById('hourly_rate');

            if (displayInput) {
                displayInput.addEventListener('input', function () {
                    const raw = displayInput.value.replace(/[^0-9]/g, '');
                    hiddenInput.value = raw;
                    displayInput.value = raw ? Number(raw).toLocaleString('en-US') : '';
                });
            }

            // ── Loading khi submit ───────────────────────────
            const editTimeForm = document.querySelector('.sc-form-edit-time');
            if (editTimeForm) {
                editTimeForm.addEventListener('submit', function () {
                    if (typeof showLoading === 'function') showLoading();
                });
            }

            document.querySelectorAll('.sc-form-salary').forEach(function (form) {
                form.addEventListener('submit', function () {
                    if (typeof showLoading === 'function') showLoading();
                });
            });
        });
    </script>
@endpush