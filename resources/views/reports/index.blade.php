@extends('layouts.app')

@section('title', 'Báo Cáo - Tổng Quan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/reports.css') }}">
@endpush

@section('content')
    <div class="report-page">

        {{-- ── HEADER ── --}}
        <div class="report-header">
            <div class="d-flex align-items-center gap-3">
                <div class="report-icon-badge">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <h1 class="report-title">Báo Cáo Tổng Quan</h1>
                    <p class="report-subtitle">
                        <i class="fa-regular fa-calendar me-1"></i>
                        Tháng {{ $month }}/{{ $year }} — LSSD Human Resources Management
                    </p>
                </div>
            </div>
            <div class="report-nav-tabs">
                <a href="{{ route('reports.index') }}" class="rtab active"><i class="fa-solid fa-gauge-high"></i> Tổng
                    Quan</a>
                <a href="{{ route('reports.attendance') }}" class="rtab"><i class="fa-solid fa-clock"></i> Chấm Công</a>
                <a href="{{ route('reports.payroll') }}" class="rtab"><i class="fa-solid fa-sack-dollar"></i> Lương</a>
                <a href="{{ route('reports.employees') }}" class="rtab"><i class="fa-solid fa-users"></i> Nhân Sự</a>
            </div>
        </div>

        {{-- ── KPIs ── --}}
        <div class="kpi-grid">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $totalEmployees }}</div>
                    <div class="kpi-label">Tổng Nhân Viên</div>
                    <div class="kpi-sub">{{ $activeEmployeesThisMonth }} đã chấm công tháng này</div>
                </div>
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($totalHoursThisMonth, 1) }}<span class="kpi-unit">h</span></div>
                    <div class="kpi-label">Tổng Giờ Làm</div>
                    <div class="kpi-sub">{{ $totalSessionsThisMonth }} phiên chấm công</div>
                </div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fa-solid fa-dollar-sign"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($totalWageThisMonth) }}<span class="kpi-unit">$</span></div>
                    <div class="kpi-label">Quỹ Lương Tháng</div>
                    <div class="kpi-sub">Tổng chi trả tháng {{ $month }}</div>
                </div>
            </div>
            <div class="kpi-card {{ $attendanceRate >= 70 ? 'kpi-green' : 'kpi-red' }}">
                <div class="kpi-icon"><i class="fa-solid fa-percent"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $attendanceRate }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-label">Tỉ Lệ Chấm Công</div>
                    <div class="kpi-sub">{{ $absentCount }} nhân viên chưa chấm</div>
                </div>
            </div>
        </div>

        {{-- ── ROW 1: Charts ── --}}
        <div class="report-grid-2">
            {{-- Chart: 6 tháng --}}
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-chart-line me-2"></i>Tổng Giờ Làm — 6 Tháng Gần Nhất</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="chartHours6M"></canvas>
                </div>
            </div>

            {{-- Chart: Phân bổ chức vụ --}}
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-chart-pie me-2"></i>Phân Bổ Nhân Sự Theo Chức Vụ</span>
                </div>
                <div class="chart-wrap chart-wrap-sm">
                    <canvas id="chartPosition"></canvas>
                </div>
            </div>
        </div>

        {{-- ── ROW 2: Top Workers + Activity Log ── --}}
        <div class="report-grid-2">
            {{-- Top 5 --}}
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-trophy me-2" style="color:#D4AF37;"></i>Top 5 Nhân Viên Tháng
                        {{ $month }}</span>
                </div>
                <div class="top-workers">
                    @foreach($topWorkers as $i => $tw)
                        @php
                            $medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
                            $emp = $tw->user?->employee;
                        @endphp
                        <div class="top-worker-row {{ $i == 0 ? 'top-worker-1st' : '' }}">
                            <span class="tw-rank">{{ $medals[$i] ?? ($i + 1) }}</span>
                            @if ($tw->user->employee->avatar)
                                <img id="avatarPreview" src="{{ asset('storage/' . $tw->user->employee->avatar) }}" alt="Avatar"
                                    class="rounded-circle" width="38" height="38"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($tw->user->employee->name_ingame ?? 'NAN') }}&background=random';">
                            @else
                                <img id="avatarPreview"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($tw->user->employee->name_ingame ?? 'NAN') }}&background=random"
                                    class="rounded-circle" width="38" height="38" alt="Default">
                            @endif
                            <div class="tw-info">
                                <div class="tw-name">{{ $emp?->name_ingame ?? $tw->user?->username ?? '—' }}</div>
                                <div class="tw-pos">{{ $emp?->position?->name_positions ?? '' }} ·
                                    {{ $emp?->rank?->name_ranks ?? '' }}
                                </div>
                            </div>
                            <div class="tw-stats">
                                <div class="tw-hours">{{ round($tw->total_hours, 1) }}h</div>
                                <div class="tw-wage">{{ number_format($tw->total_wage) }}$</div>
                            </div>
                        </div>
                    @endforeach
                    @if($topWorkers->isEmpty())
                        <div class="empty-state"><i class="fa-solid fa-inbox"></i> Chưa có dữ liệu tháng này</div>
                    @endif
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="report-card">
                <div class="report-card-header">
                    <span><i class="fa-solid fa-bolt me-2" style="color:#F0CC5A;"></i>Hoạt Động Gần Đây</span>
                </div>
                <div class="activity-log">
                    @foreach($recentLogs as $log)
                        <div class="log-item">
                            <div class="log-dot"></div>
                            <div class="log-body">
                                <div class="log-action">
                                    <span
                                        class="log-user">{{ $log->user?->employee?->name_ingame ?? $log->user?->username ?? 'System' }}</span>
                                    <span class="log-badge-action">{{ $log->action }}</span>
                                </div>
                                @if($log->detail)
                                    <!-- <div class="log-detail">{{ Str::limit($log->detail, 80) }}</div> -->
                                    <div class="log-detail">{{ $log->detail }}</div>
                                @endif
                                <div class="log-time"><i
                                        class="fa-regular fa-clock me-1"></i>{{ $log->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                    @if($recentLogs->isEmpty())
                        <div class="empty-state"><i class="fa-solid fa-inbox"></i> Không có hoạt động nào</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── ROW 3: Daily chart ── --}}
        <div class="report-card">
            <div class="report-card-header">
                <span><i class="fa-solid fa-chart-column me-2"></i>Tổng Giờ Làm Theo Ngày — Tháng
                    {{ $month }}/{{ $year }}</span>
            </div>
            <div class="chart-wrap">
                <canvas id="chartDailyHours"></canvas>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const gold = '#D4AF37', goldDim = 'rgba(212,175,55,0.15)', blue = '#4A90D9', green = '#27AE60';
        const gridColor = 'rgba(255,255,255,0.06)', textColor = '#8A9BBD';

        Chart.defaults.color = textColor;
        Chart.defaults.font.family = "'Inter', sans-serif";

        // ── 6-Month Hours Chart ──
        const d6 = @json($last6Months);
        new Chart(document.getElementById('chartHours6M'), {
            type: 'line',
            data: {
                labels: d6.map(x => x.label),
                datasets: [{
                    label: 'Giờ làm',
                    data: d6.map(x => x.hours),
                    borderColor: gold, backgroundColor: goldDim,
                    borderWidth: 2, fill: true,
                    tension: 0.4, pointBackgroundColor: gold, pointRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}h` } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + 'h' } }
                }
            }
        });

        // ── Position Pie Chart ──
        const pos = @json($byPosition);
        const posLabels = Object.keys(pos), posData = Object.values(pos);
        const posColors = ['#D4AF37', '#4A90D9', '#27AE60', '#E74C3C', '#9B59B6', '#E67E22', '#1ABC9C', '#E91E63', '#607D8B'];
        new Chart(document.getElementById('chartPosition'), {
            type: 'doughnut',
            data: {
                labels: posLabels,
                datasets: [{ data: posData, backgroundColor: posColors, borderColor: '#111827', borderWidth: 3 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, padding: 14, color: textColor, font: { size: 12 } } }
                }
            }
        });

        // ── Daily Hours Bar Chart ──
        const dd = @json($dailyHours);
        const ddLabels = Object.keys(dd).map(d => d.slice(5));
        const ddValues = Object.values(dd);
        new Chart(document.getElementById('chartDailyHours'), {
            type: 'bar',
            data: {
                labels: ddLabels,
                datasets: [{
                    label: 'Giờ làm',
                    data: ddValues,
                    backgroundColor: goldDim, borderColor: gold, borderWidth: 1.5, borderRadius: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}h` } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + 'h' } }
                }
            }
        });
    </script>
@endpush