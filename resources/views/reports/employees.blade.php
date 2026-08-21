@extends('layouts.admin')
@section('title', 'Báo Cáo Nhân Sự')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/reports.css') }}">
@endpush

@section('content')
    <div class="report-page">

        <div class="report-header">
            <div class="d-flex align-items-center gap-3">
                <div class="report-icon-badge"><i class="fa-solid fa-users"></i></div>
                <div>
                    <h1 class="report-title">Báo Cáo Nhân Sự</h1>
                    <p class="report-subtitle">Tình trạng quân số, gia nhập & vắng mặt</p>
                </div>
            </div>
            <div class="report-nav-tabs">
                <a href="{{ route('reports.index') }}" class="rtab"><i class="fa-solid fa-gauge-high"></i> Tổng Quan</a>
                <a href="{{ route('reports.attendance') }}" class="rtab"><i class="fa-solid fa-clock"></i> Chấm Công</a>
                <a href="{{ route('reports.payroll') }}" class="rtab"><i class="fa-solid fa-sack-dollar"></i> Lương</a>
                <a href="{{ route('reports.employees') }}" class="rtab active"><i class="fa-solid fa-users"></i> Nhân Sự</a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="report-filter-bar">
            <form method="GET" action="{{ route('reports.employees') }}" class="d-flex align-items-center gap-3 flex-wrap">
                <div class="filter-group"><label>Tháng</label>
                    <select name="month" class="filter-select">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>@endfor
                    </select>
                </div>
                <div class="filter-group"><label>Năm</label>
                    <select name="year" class="filter-select">
                        @foreach(range(date('Y'), 2024, -1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass me-2"></i>Xem</button>
            </form>
        </div>

        {{-- KPIs --}}
        <div class="kpi-grid">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $employees->count() }}</div>
                    <div class="kpi-label">Quân Số Hiện Tại</div>
                    <div class="kpi-sub">Nhân viên đang hoạt động</div>
                </div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fa-solid fa-user-plus"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $newThisMonth->count() }}</div>
                    <div class="kpi-label">Gia Nhập Tháng {{ $month }}</div>
                    <div class="kpi-sub">Tân binh mới</div>
                </div>
            </div>
            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="fa-solid fa-user-minus"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $deletedThisMonth->count() }}</div>
                    <div class="kpi-label">Rời Đơn Vị</div>
                    <div class="kpi-sub">Trong tháng {{ $month }}/{{ $year }}</div>
                </div>
            </div>
            <div class="kpi-card {{ $absentEmployees->count() == 0 ? 'kpi-green' : 'kpi-red' }}">
                <div class="kpi-icon"><i class="fa-solid fa-user-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $absentEmployees->count() }}</div>
                    <div class="kpi-label">Vắng Kỳ Này</div>
                    <div class="kpi-sub">Chưa chấm công lần nào</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="report-grid-2">
            <div class="report-card">
                <div class="report-card-header"><span><i class="fa-solid fa-chart-line me-2"></i>Biến Động Quân Số 6
                        Tháng</span></div>
                <div class="chart-wrap"><canvas id="chartGrowth"></canvas></div>
            </div>
            <div class="report-card">
                <div class="report-card-header"><span><i class="fa-solid fa-chart-bar me-2"></i>Giờ Làm Theo Chức Vụ — {{ $period['label'] }}</span></div>
                <div class="chart-wrap"><canvas id="chartHoursByPos"></canvas></div>
            </div>
        </div>

        {{-- Phân bổ chức vụ --}}
        <div class="report-grid-2">
            <div class="report-card">
                <div class="report-card-header"><span><i class="fa-solid fa-sitemap me-2"></i>Quân Số Theo Chức Vụ</span>
                </div>
                <div class="pos-breakdown">
                    @foreach($byPosition as $pos => $count)
                        @php $pct = $employees->count() > 0 ? round($count / $employees->count() * 100) : 0; @endphp
                        <div class="pos-row">
                            <div class="pos-name">{{ $pos }}</div>
                            <div class="pos-bar-wrap">
                                <div class="pos-bar" style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="pos-count">{{ $count }} <span class="text-muted">({{ $pct }}%)</span></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Nhân viên vắng --}}
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-triangle-exclamation me-2" style="color:#E74C3C;"></i>Vắng Chấm Công Kỳ Này
                        ({{ $period['label'] }})</span>
                    <span class="report-badge report-badge-red">{{ $absentEmployees->count() }}</span>
                </div>
                @if($absentEmployees->isNotEmpty())
                    <div class="absent-list">
                        @foreach($absentEmployees as $e)
                            <div class="absent-row">
                                @if ($e->avatar)
                                    <img id="avatarPreview" src="{{ asset('storage/' . $e->avatar) }}" alt="Avatar"
                                        class="rounded-circle" width="34" height="34"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($e->name_ingame ?? 'NAN') }}&background=random';"
                                        style="border:1.5px solid rgba(231,76,60,0.4);">
                                @else
                                    <img id="avatarPreview"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($e->name_ingame ?? 'NAN') }}&background=random"
                                        class="rounded-circle" width="34" height="34" alt="Default"
                                        style="border:1.5px solid rgba(231,76,60,0.4);">
                                @endif
                                <div>
                                    <div style="font-weight:600;font-size:13.5px;">{{ $e->name_ingame }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $e->position?->name_positions }} ·
                                        {{ $e->rank?->name_ranks }}</div>
                                </div>
                                <span class="status-badge status-absent ms-auto">0h</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding:28px;"><i class="fa-solid fa-check-circle"
                            style="color:#27AE60;"></i> Tất cả đã chấm công kỳ này!</div>
                @endif
            </div>
        </div>

        {{-- Nhân viên mới gia nhập --}}
        @if($newThisMonth->isNotEmpty())
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-star me-2" style="color:#D4AF37;"></i>Tân Binh Gia Nhập Tháng
                        {{ $month }}/{{ $year }}</span>
                    <span class="report-badge report-badge-green">+{{ $newThisMonth->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên In-Game</th>
                                <th>Chức Vụ</th>
                                <th>Cấp Bậc</th>
                                <th>Người Tuyển</th>
                                <th>Ngày Gia Nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newThisMonth as $i => $e)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $e->name_ingame }}</div>
                                    </td>
                                    <td><span class="pos-badge">{{ $e->position?->name_positions ?? '—' }}</span></td>
                                    <td style="color:var(--text-secondary);font-size:13px;">{{ $e->rank?->name_ranks ?? '—' }}</td>
                                    <td style="color:var(--text-secondary);font-size:13px;">
                                        {{ $e->userCreatedBy?->employee->name_ingame ?? '—' }}</td>
                                    <td><span class="date-badge">{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Bảng tổng hợp toàn bộ nhân viên --}}
        <div class="report-card">
            <div class="report-card-header">
                <span><i class="fa-solid fa-list me-2"></i>Tổng Hợp Toàn Bộ Nhân Viên</span>
                <span class="report-badge report-badge-gold">{{ $employees->count() }} người</span>
            </div>
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nhân Viên</th>
                            <th>Chức Vụ</th>
                            <th>Cấp Bậc</th>
                            <th class="text-center">Số Ca
                            ({{ $period['label'] }})</th>
                            <th class="text-center">Giờ Làm
                            ({{ $period['label'] }})</th>
                            <th class="text-center">Tình Trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $i => $e)
                            @php
                                $hrs = $hoursMap[$e->user_id] ?? null;
                                $h = $hrs ? round($hrs->total_hours, 1) : 0;
                                $sess = $hrs ? $hrs->sessions : 0;
                            @endphp
                            <tr class="{{ $h == 0 ? 'row-absent' : '' }}">
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                         @if ($e->avatar)
                                            <img id="avatarPreview" src="{{ asset('storage/' . $e->avatar) }}" alt="Avatar"
                                                class="rounded-circle" width="34" height="34"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($e->name_ingame ?? 'NAN') }}&background=random';"
                                                style="border:1.5px solid rgba(231,76,60,0.4);">
                                        @else
                                            <img id="avatarPreview"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($e->name_ingame ?? 'NAN') }}&background=random"
                                                class="rounded-circle" width="34" height="34" alt="Default"
                                                style="border:1.5px solid rgba(231,76,60,0.4);">
                                        @endif
                                        <span style="font-weight:600;font-size:13.5px;">{{ $e->name_ingame }}</span>
                                    </div>
                                </td>
                                <td><span class="pos-badge">{{ $e->position?->name_positions ?? '—' }}</span></td>
                                <td style="font-size:13px;color:var(--text-secondary);">{{ $e->rank?->name_ranks ?? '—' }}</td>
                                <td class="text-center" style="font-size:13.5px;">{{ $sess }}</td>
                                <td class="text-center"><span class="{{ $h == 0 ? 'text-danger' : '' }}"
                                        style="font-weight:600;">{{ $h }}h</span></td>
                                <td class="text-center">
                                    @if($h == 0)
                                        <span class="status-badge status-absent">Vắng</span>
                                    @elseif($h < 5)
                                        <span class="status-badge status-low">Thấp</span>
                                    @elseif($h >= 20)
                                        <span class="status-badge status-top">Xuất Sắc</span>
                                    @else
                                        <span class="status-badge status-ok">Bình Thường</span>
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

        const gt = @json($growthTrend);
        new Chart(document.getElementById('chartGrowth'), {
            type: 'line',
            data: {
                labels: gt.map(x => x.label),
                datasets: [{
                    label: 'Quân số', data: gt.map(x => x.count),
                    borderColor: gold, backgroundColor: goldDim, borderWidth: 2,
                    fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: gold
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { color: gridColor } }, y: { grid: { color: gridColor }, ticks: { stepSize: 1 } } }
            }
        });

        const sp = @json($sessionsByPosition);
        const colors = ['#D4AF37', '#4A90D9', '#27AE60', '#E74C3C', '#9B59B6', '#E67E22', '#1ABC9C', '#E91E63', '#607D8B'];
        new Chart(document.getElementById('chartHoursByPos'), {
            type: 'bar',
            data: {
                labels: Object.keys(sp),
                datasets: [{
                    label: 'Tổng giờ', data: Object.values(sp),
                    backgroundColor: colors.slice(0, Object.keys(sp).length), borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}h` } } },
                scales: { x: { grid: { color: gridColor }, ticks: { callback: v => v + 'h' } }, y: { grid: { color: gridColor } } }
            }
        });
    </script>
@endpush