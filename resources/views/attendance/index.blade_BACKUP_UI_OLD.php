@extends('layouts.app')

@section('title', 'Chấm Công')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row group-function p-2">
            <div class="col-md-10 title-attendance gap-2">
                <p>Trực Tiếp Tháng {{ now()->format('m') }}: <strong>{{ number_format($monthlyTotal) }}$</strong></p>
                <p>Chức Vụ: <strong>{{ auth()->user()->position->name_positions ?? '-' }} -
                        {{ auth()->user()->employee->rank->name_ranks ?? '-' }}</strong></p>
                <p>Hệ Số Lương: <strong>{{ number_format($heSoLuong ?? 0) }}$/1h</strong>
                    <small>({{ number_format($maxHourPerDay ?? 0, 2) }}h/1day)</small>
                </p>
                <p>Sự Nghiệp: <strong>{{ number_format($totalLuong) }}$</strong></p>
            </div>

            <div class="col-md-2 d-flex justify-content-end">
                <form method="POST" action="{{ route('attendance.check') }}">
                    @csrf
                    @php
                        use Illuminate\Support\Facades\Http;

                        $isFull = $totalTodayDuration >= $maxHourPerDay;

                        $discordId = auth()->user()->employee->discord_id ?? null;
                        $discordID_TEST = 1282164227175616624;
                        $isPlayingGame = false;
                        $gameName = null;
                        $gameDetails = null;
                        $gameState = null;

                        if ($discordId) {
                            $response = Http::get("https://api.lanyard.rest/v1/users/$discordId");
                            $data = $response->json()['data'] ?? [];
                            $activities = $data['activities'] ?? [];

                            foreach ($activities as $activity) {
                                if (($activity['type'] ?? null) === 0) {
                                    $isPlayingGame = true;
                                    $gameName = $activity['name'] ?? null;
                                    $gameDetails = $activity['details'] ?? null;
                                    $gameState = $activity['state'] ?? null;
                                    break;
                                }
                            }
                        }
                    @endphp
                    {{-- Không có hoạt động game --}}
                    @if (!$isPlayingGame)
                        <p class="btn-timekeeping-fulltime mt-2">
                            <i class="fa-solid fa-lock"></i>
                        </p>
                    @else
                        @if ($gameName === 'GTA5VN')
                            <button type="submit" name="action" value="start"
                                class="lssd-control-btn  {{ $isFull || $ongoing ? 'd-none' : '' }}">
                                On-Duty
                            </button>

                            <button type="submit" name="action" value="stop"
                                class="bg-danger text-white lssd-control-btn {{ $isFull || !$ongoing ? 'd-none' : '' }}">
                                Off-Duty
                            </button>
                        @else
                            <p class="btn-timekeeping-fulltime mt-2">
                                <i class="fa-solid fa-lock"></i>
                            </p>
                        @endif
                    @endif

                    {{-- Đã đủ giờ --}}
                    @if ($isFull)
                        <p class="btn-timekeeping-fulltime mt-2 text-danger">
                            Đã đủ giờ hôm nay
                        </p>
                    @endif
                    <!-- TEST -->
                    <div class="test">
                        <button type="submit" name="action" value="start"
                            class="lssd-control-btn  {{ $isFull || $ongoing ? 'd-none' : '' }}">
                            On-Duty
                        </button>
                        <button type="submit" name="action" value="stop"
                            class="bg-danger text-white lssd-control-btn {{ $isFull || !$ongoing ? 'd-none' : '' }}">
                            Off-Duty
                        </button>
                    </div>
                </form>
            </div>
            <div class="text-notification mt-0 mb-0">
                <div class="discord-activity">
                    @if ($discordId === null)
                        <div class="lspd-card width-fit-ctn p-2 small text-muted">
                            Chưa liên kết discord <a href="{{ route('discord.connect') }}">Liên Kết Ngay</a>
                        </div>
                    @else
                        @if ($isPlayingGame)
                            <div class="lspd-card width-fit-ctn p-2 small text-muted">
                                <span><b class="text-success">Hoạt động:</b> {{ $gameName }} / </span>
                                @if ($gameDetails)
                                    <span><b class="text-info">Chi tiết:</b> {{ $gameDetails }} / </span>
                                @endif
                                @if ($gameState)
                                    <span><b class="text-primary">Trạng thái:</b> {{ $gameState }}</span>
                                @endif
                            </div>
                            @if ($gameName != 'GTA5VN')
                                <div class="lspd-card width-fit-ctn p-2 small text-muted">
                                    Hoạt Động <b class="text-success">{{ $gameName }}</b> Không Liên Quan Đến GTA5VN
                                </div>
                            @endif
                        @else
                            <div class="lspd-card width-fit-ctn p-2 small text-muted">
                                Xin chào
                                {{ auth()->user()->employee->discord_id ? auth()->user()->employee->discord_username : auth()->user()->employee->name_ingame ?? auth()->user()->name }}
                                Vui lòng bật hoạt động trên Discord và tham gia <a href="https://discord.gg/YK3xRYmu"
                                    class="btn btn-primary lien-ket-discord ms-2" id="discord-link">
                                    <i class="fab fa-discord"></i><b>Lanyard</b>
                                </a> để tiếp tục chấm công
                            </div>
                        @endif
                    @endif
                </div>
                <div class="lssd-card width-fit-ctn p-2 small text-muted">
                    <details>
                        <summary>Chi tiết trừ lương quá giờ</summary>
                        <ol>
                            <li>Nếu tổng giờ làm việc >= 2 (Lố 2h) - 20% số tiền lương ca đó</li>
                            <li>Nếu tổng giờ làm việc >= 3 (Lố 3h) - 50% số tiền lương ca đó</li>
                            <li>Nếu tổng giờ làm việc >= 6 (Lố 6h) - 90% số tiền lương ca đó</li>
                        </ol>
                        <p class="badge bg-warning">Lương ca = giờ * hệ số lương</p>
                        <p class="badge bg-danger">Tiền trừ từ phần trăm = Lương ca × (Phần trăm trừ) / 100</p>
                    </details>
                </div>
            </div>
        </div>
        <div class="box-employees">
            <div class="table-employees table-responsive">
                <table class="table-bordered tb-timekeeping mb-5 text-center align-middle">
                    <thead>
                        <tr class="bg-warning">
                            <th>ID</th>
                            <th>Sĩ quan</th>
                            <th>Ngày/Tháng/Năm</th>
                            <th>On-Duty</th>
                            <th>Off-Duty</th>
                            <th>Timer</th>
                            <th>Giờ</th>
                            <th>Lương</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Nhóm tất cả daily summaries theo tháng
                            $summariesByMonth = collect($dailySummaries)->groupBy(function ($summary) {
                                return \Carbon\Carbon::parse($summary['date'])->format('m/Y');
                            });
                        @endphp

                        @foreach ($summariesByMonth as $month => $monthSummaries)
                            {{-- Hàng tiêu đề tháng --}}
                            <tr class="bg_secondary_cus text-white">
                                <td colspan="9" class="text-center">
                                    <strong>Bảng chấm công tháng {{ $month }}</strong>
                                </td>
                            </tr>

                            @foreach ($monthSummaries as $summary)
                                @foreach ($summary['attendances'] as $att)
                                    <tr>
                                        <td><small>{{ $att->id }}</small></td>
                                        <td>{{ $att->user->employee->name_ingame ?? $att->user->username }}</td>
                                        <td>{{ date_format($att->date, 'd/m/Y') }}</td>
                                        <td>{{ $att->check_in->format('H:i:s - d/m') }}</td>
                                        <td>{{ optional($att->check_out)->format('H:i:s - d/m') ?? 'Đang làm việc...' }}</td>
                                        <td
                                            class="text-primary {{ $att->id == ($ongoing->id ?? null) && is_null($att->check_out) ? 'text-danger' : '' }}">
                                            @if(isset($ongoing) && $ongoing->id == $att->id && is_null($att->check_out))
                                                {{-- Đây là hàng đang On-Duty → đặt id duy nhất --}}
                                                <div id="timer-main">00:00:00</div>
                                            @else
                                                {{-- Dòng lịch sử (đã off) hiển thị duration tĩnh --}}
                                                <div class="timer-static">
                                                    {{ $att->check_out ? \Carbon\Carbon::parse($att->check_in)->diff(\Carbon\Carbon::parse($att->check_out))->format('%H:%I:%S') : '00:00:00' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ number_format($att->duration, 2) }}h</td>
                                        <td class="text-success">{{ number_format($att->wage, 0) }}$</td>
                                        <td>
                                            @if ($att->status == 'Hoàn thành')
                                                <span class="bg-success">{{ $att->status }}</span>
                                            @elseif ($att->status == 'Đang On-Duty')
                                                <span class="bg-success">{{ $att->status }}</span>
                                            @elseif (Str::contains($att->status, 'Quản Lý'))
                                                <span class="bg- p-1 text-danger" style="border-radius: 8px;">{{ $att->status }}</span>
                                            @elseif (Str::contains($att->status, 'Tự Động'))
                                                <span class="bg- p-1 text-success" style="border-radius: 8px;">{{ $att->status }}</span>
                                            @elseif (Str::contains($att->status, 'Dư'))
                                                <span class="bg- p-1 text-danger" style="border-radius: 8px;">{{ $att->status }}</span>
                                            @elseif (Str::startsWith($att->status, 'Còn'))
                                                <span class="bg- text-primary">{{ $att->status }}</span>
                                            @else
                                                {{ $att->status }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Tổng từng ngày --}}
                                <tr class="total-day-timekeeping">
                                    <td colspan="5">
                                        Tổng ngày:
                                        {{ \Carbon\Carbon::parse($summary['date'])->format('d/m/Y') }}
                                    </td>
                                    <td colspan="3">
                                        Lương ngày: {{ number_format($summary['total_wage']) }}$
                                    </td>
                                    <td>
                                        Tổng Giờ:
                                        {{ number_format($summary['total_duration'], 2) }}h/{{ $maxHourPerDay }}h
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendancesPaginated->links() }}
                </div>
            </div>
        </div>
        <div class="ket_noi_bang mt-0">
            <h5 class="p-4"><strong>LỊCH SỬ TỔNG KẾT THÁNG</strong></h5>
        </div>
        <div class="box_history_time table-responsive">
            <table class="tb_total_month">
                <thead>
                    <tr class="table-info">
                        <th>Tháng/Năm</th>
                        <th>Tổng giờ</th>
                        <th>Tổng lương</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlySummaries as $summary)
                        <tr>
                            <td>{{ str_pad($summary->month, 2, '0', STR_PAD_LEFT) }}/{{ $summary->year }}</td>
                            <td>{{ number_format($summary->total_hours, 2) }}h</td>
                            <td class="text-success">{{ number_format($summary->total_wage) }}$</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
<script>
    const CHECK_IN_TIME = {{ $ongoing ? strtotime($ongoing->check_in) : 'null' }};
</script>
@push('scripts')
    <script>
        let timerInterval = null;
        const timerEl = document.getElementById('timer-main');

        function formatHhMmSs(seconds) {
            const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            return `${hrs}:${mins}:${secs}`;
        }

        function updateTimerDisplay(seconds) {
            if (!timerEl) return; // nếu không có phần tử (không phải dòng ongoing) → bỏ qua
            timerEl.textContent = formatHhMmSs(seconds);
        }

        function startServerTimer() {
            // CHECK_IN_TIME có thể là số hoặc null
            if (typeof CHECK_IN_TIME === 'undefined' || CHECK_IN_TIME === null) return;

            // trước khi tạo interval, cập nhật ngay 1 lần
            const now0 = Math.floor(Date.now() / 1000);
            let elapsed0 = now0 - CHECK_IN_TIME;
            if (elapsed0 < 0) elapsed0 = 0;
            updateTimerDisplay(elapsed0);

            // clear nếu có interval cũ (an toàn)
            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                const now = Math.floor(Date.now() / 1000);
                let elapsed = now - CHECK_IN_TIME;
                if (!Number.isFinite(elapsed) || elapsed < 0) elapsed = 0;
                updateTimerDisplay(elapsed);
            }, 1000);
        }

        // Gắn sự kiện: nếu nút tồn tại thì submit form (server sẽ trả về check_in mới)
        document.getElementById("startBtn")?.addEventListener("click", function (e) {
            // submit bình thường — server sẽ xử lý, cập nhật check_in và trả page mới
            // không preventDefault để giữ POST và bảo đảm server lưu check_in
        });

        document.getElementById("stopBtn")?.addEventListener("click", function (e) {
            // submit bình thường
        });

        // Khi load: khởi timer nếu có CHECK_IN_TIME và phần tử hiển thị
        window.addEventListener('load', function () {
            // nếu phần tử timerEl tồn tại và CHECK_IN_TIME hợp lệ → chạy
            if (timerEl && typeof CHECK_IN_TIME !== 'undefined' && CHECK_IN_TIME !== null) {
                startServerTimer();
            }
        });

        // Dọn dẹp khi unload (không bắt buộc nhưng tốt)
        window.addEventListener('beforeunload', () => {
            if (timerInterval) clearInterval(timerInterval);
        });

    </script>
@endpush