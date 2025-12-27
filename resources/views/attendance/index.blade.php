@extends('layouts.app')

@section('title', 'Chấm Công')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row group-function p-2">
            <div class="col-md-10 title-attendance gap-2">
                {{-- <p>Ca trong ngày hôm nay ({{ \Carbon\Carbon::parse(now())->format('d/m/Y') }})</p> --}}
                <p>Trực Tiếp Tháng {{ now()->format('m') }}: <strong>{{ number_format($monthlyTotal) }}$</strong></p>
                <p>Chức Vụ: <strong>{{ auth()->user()->position->name_positions ?? '-' }} -
                        {{ auth()->user()->employee->rank->name_ranks ?? '-' }}</strong></p>
                <p>Hệ Số Lương: <strong>{{ number_format($heSoLuong ?? 0) }}$/1h</strong>
                    <small>({{ number_format(auth()->user()->positionSalaryConfig()->max_hours_per_day ?? 0) }}h/1day)</small>
                </p>
                {{-- <p>Tính Hệ Số Lương: <strong>{{ number_format($heSoLuong) }} /(Chia) Số Giờ</strong></p>--}}
                <p>Sự Nghiệp: <strong>{{ number_format($totalLuong) }}$</strong></p>
            </div>

            <div class="col-md-2 d-flex justify-content-end">
                <form method="POST" action="{{ route('attendance.check') }}">
                    @csrf
                    @php
                        $isFull = $totalTodayDuration >= $maxHourPerDay;
                    @endphp

                    <button type="submit" name="action" value="start" id="startBtn"
                        class="btn-timekeeping-onduty {{ $isFull || $ongoing ? 'd-none' : '' }}">
                        On-Duty
                    </button>

                    <button type="submit" name="action" value="stop" id="stopBtn"
                        class="btn-timekeeping-offduty {{ $isFull || !$ongoing ? 'd-none' : '' }}">
                        Off-Duty
                    </button>

                    @if($isFull)
                        <p class="btn-timekeeping-fulltime mt-2">Đã đủ giờ hôm nay</p>
                    @endif
                </form>
            </div>
            <p class="text-notification pt-3 mb-0">
                Vui lòng nhấn F5 trước khi On-Off Duty để làm mới trang nếu có vấn đề về hiển thị dữ liệu.
            </p>
        </div>
        <div class="box-employees">
            <div class="table-employees table-responsive">
                <table class="table-bordered tb-timekeeping mb-5 text-center align-middle">
                    <thead>
                        <tr class="bg-warning">
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
                                <td colspan="8" class="text-center">
                                    <strong>Bảng chấm công tháng {{ $month }}</strong>
                                </td>
                            </tr>

                            @foreach ($monthSummaries as $summary)
                                @foreach ($summary['attendances'] as $att)
                                    <tr>
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
                                    <td colspan="4">
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
                        {{--
                        @can('manage-attendance')
                        <th>Xử lý</th>
                        @endcan
                        --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlySummaries as $summary)
                        <tr>
                            <td>{{ str_pad($summary->month, 2, '0', STR_PAD_LEFT) }}/{{ $summary->year }}</td>
                            <td>{{ number_format($summary->total_hours, 2) }}h</td>
                            <td class="text-success">{{ number_format($summary->total_wage) }}$</td>

                            {{--
                            @can('manage-attendance')
                            <td>
                                <form method="POST"
                                    action="{{ route('attendance.delete-month', [$summary->month, $summary->year, $summary->user_id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">🗑️ Xóa</button>
                                </form>
                            </td>
                            @endcan
                            --}}
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

        // document.addEventListener('DOMContentLoaded', function () {
        //     Swal.fire({
        //         icon: 'info',
        //         title: 'Thông Báo',
        //         html: `<p style="text-align:left">
        //                     <strong>Nhằm mục đích tránh lỗi </strong> chấm công và đảm bảo tính chính xác trong việc ghi nhận thời gian làm việc.<br><br>
        //                     Tất cả sĩ quan <strong>On-Duty</strong> vui lòng không tiếp tục <strong>On-Duty</strong> khi chuyển sang ngày mới.<br><br>
        //                     Vui lòng <strong>kết thúc On-Duty trước 23:50</strong> và <strong>bắt đầu lại sau 00:05 sáng hôm sau</strong>.<br><br>
        //                     Xin trân trọng cảm ơn sự hợp tác của các sĩ quan.
        //                     </p>`,
        //         confirmButtonText: 'Rõ',
        //         confirmButtonColor: '#ffc107'
        //     });
        // });
        // document.addEventListener('DOMContentLoaded', function () {
        //     Swal.fire({
        //         icon: 'info',
        //         title: 'Thông Báo',
        //         html: `<p style="text-align:left">
        //                                Nhấn F5 để làm mới trang nếu có vấn đề về hiển thị dữ liệu.<br>
        //                                <strong>Vui lòng Nhấn F5 hoặc Tải lại trang trước khi On-Duty</strong>
        //                                 </p>`,
        //         confirmButtonText: 'Rõ',
        //         confirmButtonColor: '#ffc107'
        //     });
        // });
    </script>
@endpush