@extends('layouts.admin')
@section('title', 'Báo Cáo Lương')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/reports.css') }}">
@endpush

@section('content')
    <div class="report-page">

        <div class="report-header">
            <div class="d-flex align-items-center gap-3">
                <div class="report-icon-badge"><i class="fa-solid fa-sack-dollar"></i></div>
                <div>
                    <h1 class="report-title">Báo Cáo Lương</h1>
                    {{-- ✅ --}}
                    <p class="report-subtitle">So sánh quỹ lương, chi tiết từng nhân viên — {{ $selectedPeriod['label'] }}</p>
                </div>
            </div>
            <div class="report-nav-tabs">
                <a href="{{ route('reports.index') }}" class="rtab"><i class="fa-solid fa-gauge-high"></i> Tổng Quan</a>
                <a href="{{ route('reports.attendance') }}" class="rtab"><i class="fa-solid fa-clock"></i> Chấm Công</a>
                <a href="{{ route('reports.payroll') }}" class="rtab active"><i class="fa-solid fa-sack-dollar"></i> Lương</a>
                <a href="{{ route('reports.employees') }}" class="rtab"><i class="fa-solid fa-users"></i> Nhân Sự</a>
            </div>
        </div>

        {{-- ✅ Filter — tách 2 chế độ --}}
        <div class="report-filter-bar">
            @if($config->cycle_type === 'biweekly')
                <form method="GET" action="{{ route('reports.payroll') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="filter-group">
                        <label><i class="fa-solid fa-calendar-week me-1"></i>Chọn Kỳ (14 ngày)</label>
                        <select name="period_start" id="payPeriodSel" class="filter-select" onchange="syncPayPeriodEnd(this)">
                            @foreach($availablePeriods as $p)
                                <option
                                    value="{{ $p->period_start }}"
                                    data-end="{{ $p->period_end }}"
                                    {{ (request('period_start') == $p->period_start || (!request('period_start') && $loop->first)) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($p->period_start)->format('d/m') }}
                                    –
                                    {{ \Carbon\Carbon::parse($p->period_end)->format('d/m/Y') }}
                                </option>
                            @endforeach
                            @if($availablePeriods->isEmpty())
                                <option value="{{ $selectedPeriod['period_start'] }}" data-end="{{ $selectedPeriod['period_end'] }}" selected>
                                    {{ $selectedPeriod['label'] }} (hiện tại)
                                </option>
                            @endif
                        </select>
                        <input type="hidden" name="period_end" id="payPeriodEndHidden"
                            value="{{ request('period_end', $selectedPeriod['period_end']) }}">
                    </div>
                    <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass me-2"></i>Xem</button>
                    <a href="{{ route('reports.payroll', ['period_start' => $selectedPeriod['period_start'], 'period_end' => $selectedPeriod['period_end'], 'export' => 1]) }}"
                        class="filter-btn-outline">
                        <i class="fa-solid fa-file-csv me-2"></i>Xuất CSV
                    </a>
                </form>
            @else
                <form method="GET" action="{{ route('reports.payroll') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="filter-group">
                        <label>Tháng</label>
                        <select name="month" class="filter-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Năm</label>
                        <select name="year" class="filter-select">
                            @foreach($availableMonths->pluck('year')->unique()->sort()->reverse() as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass me-2"></i>Xem</button>
                    <a href="{{ route('reports.payroll', ['month' => $month, 'year' => $year, 'export' => 1]) }}"
                        class="filter-btn-outline">
                        <i class="fa-solid fa-file-csv me-2"></i>Xuất CSV
                    </a>
                </form>
            @endif
        </div>

        {{-- ✅ KPIs --}}
        <div class="kpi-grid">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="fa-solid fa-vault"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($totalFund) }}<span class="kpi-unit">$</span></div>
                    {{-- ✅ --}}
                    <div class="kpi-label">Quỹ Lương {{ $selectedPeriod['label'] }}</div>
                    <div class="kpi-sub">Tổng chi trả</div>
                </div>
            </div>
            <div class="kpi-card {{ $fundDiff >= 0 ? 'kpi-green' : 'kpi-red' }}">
                <div class="kpi-icon"><i class="fa-solid fa-arrow-trend-{{ $fundDiff >= 0 ? 'up' : 'down' }}"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $fundDiff >= 0 ? '+' : '' }}{{ number_format($fundDiff) }}<span class="kpi-unit">$</span></div>
                    {{-- ✅ So với kỳ trước động --}}
                    <div class="kpi-label">So Với Kỳ Trước</div>
                    <div class="kpi-sub">
                        {{ $prevPeriod['label'] }} ·
                        {{ $fundDiffPct !== null ? ($fundDiff >= 0 ? '+' : '') . $fundDiffPct . '%' : 'Không có dữ liệu' }}
                    </div>
                </div>
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                <div class="kpi-body">
                    @php $paidCount = collect($payrollData)->filter(fn($p) => $p->total_wage > 0)->count(); @endphp
                    <div class="kpi-value">{{ $paidCount }}</div>
                    <div class="kpi-label">Nhân Viên Được Trả</div>
                    <div class="kpi-sub">/ {{ $users->count() }} tổng</div>
                </div>
            </div>
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="fa-solid fa-calculator"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $paidCount > 0 ? number_format((int) ($totalFund / $paidCount)) : '—' }}<span class="kpi-unit">$</span></div>
                    <div class="kpi-label">Lương TB Mỗi Người</div>
                    {{-- ✅ --}}
                    <div class="kpi-sub">{{ $selectedPeriod['label'] }}</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="report-grid-2">
            <div class="report-card">
                {{-- ✅ --}}
                <div class="report-card-header"><span><i class="fa-solid fa-chart-line me-2"></i>Xu Hướng Quỹ Lương —
                    {{ $config->cycle_type === 'biweekly' ? '6 Kỳ Gần Nhất' : '6 Tháng Gần Nhất' }}
                </span></div>
                <div class="chart-wrap"><canvas id="chartFundTrend"></canvas></div>
            </div>
            <div class="report-card">
                <div class="report-card-header"><span><i class="fa-solid fa-chart-bar me-2"></i>Lương Theo Chức Vụ</span></div>
                <div class="chart-wrap"><canvas id="chartWageByPos"></canvas></div>
            </div>
        </div>

        {{-- ✅ Bảng lương chi tiết --}}
        <div class="report-card">
            <div class="report-card-header">
                <span><i class="fa-solid fa-table me-2"></i>Bảng Lương Chi Tiết — {{ $selectedPeriod['label'] }}</span>
                <span class="report-badge report-badge-gold">{{ $users->count() }} nhân viên</span>
            </div>
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nhân Viên</th>
                            <th>Chức Vụ</th>
                            <th class="text-center">Hệ Số ($/h)</th>
                            <th class="text-center">Giờ Làm</th>
                            {{-- ✅ --}}
                            <th class="text-right">Lương Kỳ Này</th>
                            <th class="text-right">Kỳ Trước<br><small style="font-weight:400;font-size:11px;opacity:.7">{{ $prevPeriod['label'] }}</small></th>
                            <th class="text-center">Chênh Lệch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $u)
                            @php $p = $payrollData[$u->id]; @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($u->employee->avatar)
                                            <img src="{{ asset('storage/' . $u->employee->avatar) }}" alt="Avatar"
                                                class="rounded-circle" width="32" height="32"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($u->employee->name_ingame ?? 'NAN') }}&background=random';"
                                                style="border:1.5px solid rgba(212,175,55,0.4);">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($u->employee->name_ingame ?? 'NAN') }}&background=random"
                                                class="rounded-circle" width="32" height="32" alt="Default"
                                                style="border:1.5px solid rgba(212,175,55,0.4);">
                                        @endif
                                        <div>
                                            <div style="font-weight:600;font-size:13.5px;">{{ $u->employee?->name_ingame ?? $u->username }}</div>
                                            <div style="font-size:11px;color:var(--text-muted);">{{ $u->username }} - {{ $u->employee->rank?->name_ranks ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="pos-badge">{{ $u->employee?->position?->name_positions ?? '—' }}</span></td>
                                <td class="text-center" style="color:var(--gold-light);font-size:13px;">{{ number_format($p->rate) }}$</td>
                                <td class="text-center">{{ $p->total_hours }}h</td>
                                <td class="text-right"><span class="wage-val">{{ number_format($p->total_wage) }}$</span></td>
                                <td class="text-right" style="color:var(--text-muted);font-size:13px;">
                                    {{ $p->prev_wage > 0 ? number_format($p->prev_wage) . '$' : '—' }}
                                </td>
                                <td class="text-center">
                                    @if($p->diff_pct !== null)
                                        <span class="diff-badge {{ $p->diff >= 0 ? 'diff-up' : 'diff-down' }}">
                                            <i class="fa-solid fa-arrow-{{ $p->diff >= 0 ? 'up' : 'down' }}"></i>
                                            {{ abs($p->diff_pct) }}%
                                        </span>
                                    @else
                                        <span style="color:var(--text-muted);font-size:12px;">Mới</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-total-row">
                            <td colspan="5" style="font-weight:700;letter-spacing:0.06em;">TỔNG CỘNG</td>
                            <td class="text-right"><span class="wage-val">{{ number_format($totalFund) }}$</span></td>
                            <td class="text-right" style="color:var(--text-muted);">
                                {{ $prevFund > 0 ? number_format($prevFund) . '$' : '—' }}
                            </td>
                            <td class="text-center">
                                @if($fundDiffPct !== null)
                                    <span class="diff-badge {{ $fundDiff >= 0 ? 'diff-up' : 'diff-down' }}">
                                        <i class="fa-solid fa-arrow-{{ $fundDiff >= 0 ? 'up' : 'down' }}"></i>
                                        {{ abs($fundDiffPct) }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const gold = '#D4AF37', goldDim = 'rgba(212,175,55,0.18)', gridColor = 'rgba(255,255,255,0.06)', textColor = '#8A9BBD';
        Chart.defaults.color = textColor;

        const ft = @json($fundTrend);
        new Chart(document.getElementById('chartFundTrend'), {
            type: 'line',
            data: {
                labels: ft.map(x => x.label),
                datasets: [{
                    label: 'Quỹ lương ($)', data: ft.map(x => x.wage),
                    borderColor: gold, backgroundColor: goldDim, borderWidth: 2,
                    fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: gold
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw.toLocaleString()}$` } } },
                scales: {
                    x: { grid: { color: gridColor } },
                    y: { grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() + '$' } }
                }
            }
        });

        const wp = @json($wageByPosition);
        const colors = ['#D4AF37','#4A90D9','#27AE60','#E74C3C','#9B59B6','#E67E22','#1ABC9C','#E91E63','#607D8B'];
        new Chart(document.getElementById('chartWageByPos'), {
            type: 'bar',
            data: {
                labels: Object.keys(wp),
                datasets: [{
                    label: 'Tổng lương ($)', data: Object.values(wp),
                    backgroundColor: colors.slice(0, Object.keys(wp).length),
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw.toLocaleString()}$` } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() + '$' } },
                    y: { grid: { color: gridColor } }
                }
            }
        });

        // ✅ Đồng bộ period_end khi chọn kỳ biweekly
        function syncPayPeriodEnd(sel) {
            const opt = sel.options[sel.selectedIndex];
            const endInput = document.getElementById('payPeriodEndHidden');
            if (endInput && opt.dataset.end) endInput.value = opt.dataset.end;
        }
        const initPaySel = document.getElementById('payPeriodSel');
        if (initPaySel) syncPayPeriodEnd(initPaySel);
    </script>
@endpush
