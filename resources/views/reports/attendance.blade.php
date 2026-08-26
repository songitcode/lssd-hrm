@extends('layouts.admin')
@section('title', 'Báo Cáo Chấm Công')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/reports.css') }}">
@endpush

@section('content')
    <div class="report-page">

        <div class="report-header">
            <div class="d-flex align-items-center gap-3">
                <div class="report-icon-badge"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <h1 class="report-title">Báo Cáo Chấm Công</h1>
                    {{-- ✅ --}}
                    <p class="report-subtitle">Chi tiết giờ làm việc từng nhân viên — {{ $period['label'] }}</p>
                </div>
            </div>
            <div class="report-nav-tabs">
                <a href="{{ route('reports.index') }}" class="rtab"><i class="fa-solid fa-gauge-high"></i> Tổng Quan</a>
                <a href="{{ route('reports.attendance') }}" class="rtab active"><i class="fa-solid fa-clock"></i> Chấm Công</a>
                <a href="{{ route('reports.payroll') }}" class="rtab"><i class="fa-solid fa-sack-dollar"></i> Lương</a>
                <a href="{{ route('reports.employees') }}" class="rtab"><i class="fa-solid fa-users"></i> Nhân Sự</a>
            </div>
        </div>

        {{-- ✅ Filter bar — tách 2 chế độ monthly / biweekly --}}
        <div class="report-filter-bar">
            @if($config->cycle_type === 'biweekly')
                {{-- Biweekly: chọn từ danh sách kỳ đã có --}}
                <form method="GET" action="{{ route('reports.attendance') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="filter-group">
                        <label><i class="fa-solid fa-calendar-week me-1"></i>Chọn Kỳ (14 ngày)</label>
                        <select name="period_start" id="periodStartSel" class="filter-select" onchange="syncPeriodEnd(this)">
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
                                <option value="{{ $period['period_start'] }}" data-end="{{ $period['period_end'] }}" selected>
                                    {{ $period['label'] }} (hiện tại)
                                </option>
                            @endif
                        </select>
                        <input type="hidden" name="period_end" id="periodEndHidden"
                            value="{{ request('period_end', $period['period_end']) }}">
                    </div>
                    <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass me-2"></i>Xem Báo Cáo</button>
                    <a href="{{ route('reports.attendance', ['period_start' => $period['period_start'], 'period_end' => $period['period_end'], 'export' => 1]) }}"
                        class="filter-btn-outline">
                        <i class="fa-solid fa-file-csv me-2"></i>Xuất CSV
                    </a>
                </form>
            @else
                {{-- Monthly: chọn tháng / năm như cũ --}}
                <form method="GET" action="{{ route('reports.attendance') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar-days me-1"></i>Chọn Tháng</label>
                        <select name="month" class="filter-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar me-1"></i>Năm</label>
                        <select name="year" class="filter-select">
                            @foreach($availableMonths->pluck('year')->unique()->sort()->reverse() as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass me-2"></i>Xem Báo Cáo</button>
                    <a href="{{ route('reports.attendance', ['month' => $month, 'year' => $year, 'export' => 1]) }}"
                        class="filter-btn-outline">
                        <i class="fa-solid fa-file-csv me-2"></i>Xuất CSV
                    </a>
                </form>
            @endif
        </div>

        {{-- KPIs --}}
        <div class="kpi-grid">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($totalHours, 1) }}<span class="kpi-unit">h</span></div>
                    <div class="kpi-label">Tổng Giờ Làm</div>
                    <div class="kpi-sub">{{ $totalSessions }} phiên chấm công</div>
                </div>
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ round($avgHourPerDay, 1) }}<span class="kpi-unit">h/ngày</span></div>
                    <div class="kpi-label">Trung Bình Mỗi Ngày</div>
                    <div class="kpi-sub">Toàn bộ nhân viên</div>
                </div>
            </div>
            <div class="kpi-card {{ $zeroHourCount == 0 ? 'kpi-green' : 'kpi-red' }}">
                <div class="kpi-icon"><i class="fa-solid fa-user-slash"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $zeroHourCount }}</div>
                    <div class="kpi-label">Chưa Chấm Công</div>
                    {{-- ✅ --}}
                    <div class="kpi-sub">Trong kỳ {{ $period['label'] }}</div>
                </div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fa-solid fa-dollar-sign"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($totalWage) }}<span class="kpi-unit">$</span></div>
                    <div class="kpi-label">Tổng Lương</div>
                    {{-- ✅ --}}
                    <div class="kpi-sub">{{ $period['label'] }}</div>
                </div>
            </div>
        </div>

        {{-- Chart giờ theo ngày --}}
        {{--
        <div class="report-card">
            <div class="report-card-header">
                <span><i class="fa-solid fa-chart-column me-2"></i>Giờ Làm Theo Ngày — {{ $period['label'] }}</span>
            </div>
            <div class="chart-wrap">
                <canvas id="chartDaily"></canvas>
            </div>
        </div>
        --}}
        {{-- Bảng chi tiết --}}
        <div class="report-card">
            <div class="report-card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-table me-2"></i>Chi Tiết Chấm Công Từng Nhân Viên</span>
                <div class="d-flex gap-2">
                    <span class="report-badge report-badge-gold">{{ $users->count() }} nhân viên</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nhân Viên</th>
                            <th>Chức Vụ</th>
                            <th>Cấp Bậc</th>
                            <th class="text-center">Số Ca</th>
                            <th class="text-center">Tổng Giờ</th>
                            <th class="text-center">Giờ/Ca TB</th>
                            <th class="text-right">Lương</th>
                            <th class="text-center">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $u)
                            @php $s = $summaries[$u->id]; @endphp
                            <tr class="{{ $top3->contains($u->id) ? 'row-highlight-gold' : '' }} {{ $s->total_hours == 0 ? 'row-absent' : '' }}">
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
                                            <div style="font-weight:600; font-size:13.5px;">{{ $u->employee?->name_ingame ?? $u->username }}</div>
                                            <div style="font-size:11px; color:var(--text-muted);">{{ $u->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="pos-badge">{{ $u->employee?->position?->name_positions ?? '—' }}</span></td>
                                <td style="font-size:13px; color:var(--text-secondary);">{{ $u->employee?->rank?->name_ranks ?? '—' }}</td>
                                <td class="text-center">{{ $s->sessions }}</td>
                                <td class="text-center">
                                    <span class="hours-bar-wrap">
                                        <span class="hours-val {{ $s->total_hours == 0 ? 'text-danger' : '' }}">{{ $s->total_hours }}h</span>
                                    </span>
                                </td>
                                <td class="text-center" style="color:var(--text-secondary); font-size:13px;">
                                    {{ $s->sessions > 0 ? $s->avg_per_day . 'h' : '—' }}
                                </td>
                                <td class="text-right">
                                    <span class="wage-val">{{ number_format($s->total_wage) }}$</span>
                                </td>
                                <td class="text-center">
                                    @if($top3->contains($u->id))
                                        <span class="status-badge status-top">Top</span>
                                    @elseif($s->total_hours == 0)
                                        <span class="status-badge status-absent">Vắng</span>
                                    @elseif($s->total_hours < 5)
                                        <span class="status-badge status-low">Thấp</span>
                                    @else
                                        <span class="status-badge status-ok">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
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

        const dd = @json($dailyData);
        new Chart(document.getElementById('chartDaily'), {
            type: 'bar',
            data: {
                labels: dd.map(r => r.day.slice(5)),
                datasets: [
                    {
                        label: 'Tổng Giờ', data: dd.map(r => parseFloat(r.hours).toFixed(2)),
                        backgroundColor: goldDim, borderColor: gold, borderWidth: 1.5, borderRadius: 6, yAxisID: 'y'
                    },
                    {
                        label: 'Số Phiên', data: dd.map(r => r.sessions),
                        type: 'line', borderColor: '#4A90D9', backgroundColor: 'transparent',
                        borderWidth: 2, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#4A90D9', yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { grid: { color: gridColor } },
                    y: { grid: { color: gridColor }, ticks: { callback: v => v + 'h' } },
                    y1: { position: 'right', grid: { display: false }, ticks: { color: '#4A90D9', callback: v => v + ' ca' } }
                }
            }
        });

        // ✅ Đồng bộ period_end khi chọn kỳ biweekly
        function syncPeriodEnd(sel) {
            const opt = sel.options[sel.selectedIndex];
            const endInput = document.getElementById('periodEndHidden');
            if (endInput && opt.dataset.end) endInput.value = opt.dataset.end;
        }
        // Khởi tạo giá trị mặc định
        const initSel = document.getElementById('periodStartSel');
        if (initSel) syncPeriodEnd(initSel);
    </script>
@endpush
