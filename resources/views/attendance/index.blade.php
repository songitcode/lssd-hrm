@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row group-function p-2">
            <div class="col-md-9 title-attendance">
                {{-- <p>Ca trong ngày hôm nay ({{ \Carbon\Carbon::parse(now())->format('d/m/Y') }})</p> --}}
                <p>Lương Trực Tiếp Tháng {{ now()->format('m') }}: <strong>{{ number_format($monthlyTotal) }}$</strong></p>
                <p>Chức Vụ: <strong>{{ auth()->user()->position->name_positions ?? '-' }}</strong></p>
                <p>Hệ Số Lương: <strong>{{ number_format($heSoLuong) }}$/1h</strong></p>
                {{-- <p>Tính Hệ Số Lương: <strong>{{ number_format($heSoLuong) }} /(Chia) Số Giờ</strong></p>--}}
                <p>Tổng Sự Nghiệp: <strong>{{ number_format($totalLuong) }}$</strong></p>
            </div>

            <div class="col-md-3 d-flex justify-content-end">
                <form method="POST" action="{{ route('attendance.check') }}">
                    @csrf
                    @php
                        $buttonText = '';
                        $buttonDisabled = false;
                        $buttonClass = '';

                        if ($totalTodayDuration >= $maxHourPerDay) {
                            $buttonText = 'Đã đủ giờ';
                            $buttonDisabled = true;
                            $buttonClass .= 'btn-timekeeping-fulltime';
                        } else {
                            if (!$ongoing) {
                                $buttonText = 'On-Duty';
                                $buttonClass .= 'btn-timekeeping-onduty';
                            } else {
                                $buttonText = 'Off-Duty';
                                $buttonClass .= 'btn-timekeeping-offduty';
                            }
                        }
                    @endphp
                    <button type="submit" class="{{ $buttonClass }}" {{ $buttonDisabled ? 'disabled' : '' }}
                        @if($buttonDisabled) disabled @endif>
                        {{ $buttonText }}
                    </button>
                </form>
            </div>
        </div>
        <div class="box-employees">
            <div class="table-employees table-responsive">
                <table class="table-bordered tb-timekeeping mb-5 text-center align-middle">
                    <thead>
                        <tr class="bg-warning">
                            <th>Sĩ quan</th>
                            <th>Ngày</th>
                            <th>On-Duty</th>
                            <th>Off-Duty</th>
                            <th>Giờ</th>
                            <th>Lương</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dailySummaries as $summary)
                            @foreach ($summary['attendances'] as $att)
                                <tr>
                                    <td>{{ $att->user->employee->name_ingame ?? $att->user->username }}</td>
                                    <td>{{ date_format($att->date, 'd/m/Y') }}</td>
                                    <td>{{ $att->check_in->format('d.m.y - H:i') }}</td>
                                    <td>{{ optional($att->check_out)->format('d.m.y - H:i') ?? '-' }}</td>
                                    <td>{{ number_format($att->duration, 2) }}</td>
                                    <td>{{ number_format($att->wage, 0) }}$</td>
                                    <td>
                                        @if ($att->status == 'Hoàn thành')
                                            <span class="bg-success">{{ $att->status }}</span>
                                        @elseif ($att->status == 'Đang On-Duty')
                                            <span class="bg-primary">{{ $att->status }}</span>
                                        @elseif(Str::startsWith($att->status, 'Còn'))
                                            <span class="bg-warning">{{ $att->status }}</span>
                                        @else
                                            {{ $att->status }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Tổng từng ngày (màu vàng nhạt) --}}
                            <tr class="total-day-timekeeping ">
                                <td colspan="2">Tổng ngày:<strong>
                                        {{ \Carbon\Carbon::parse($summary['date'])->format('d/m/Y') }}</strong></td>
                                <td colspan="4">Lương ngày: <strong>{{ number_format($summary['total_wage']) }}$</strong></td>
                                {{--
                                @if ($summary['total_duration'] == $maxHourPerDay)
                                <td class="glow">
                                    Tổng Giờ:
                                    <strong>{{ number_format($summary['total_duration'], 2) }}h/{{ $maxHourPerDay }}h</strong>
                                </td>
                                @else
                                <td>
                                    Tổng Giờ:
                                    <strong>{{ number_format($summary['total_duration'], 2) }}h/{{ $maxHourPerDay }}h</strong>
                                </td>
                                @endif
                                --}}
                                <td>
                                    Tổng Giờ:
                                    <strong>{{ number_format($summary['total_duration'], 2) }}h/{{ $maxHourPerDay }}h</strong>
                                </td>
                            </tr>
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
                            <td>{{ number_format($summary->total_wage) }}$</td>

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