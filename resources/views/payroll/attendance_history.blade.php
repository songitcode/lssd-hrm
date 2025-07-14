@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payrol_page.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="group-function d-flex flex-column flex-lg-row justify-content-between align-items-start">
            <h3>Lịch Sử Chấm Công - {{ $user->employee->name_ingame ?? $user->username }} (Tháng
                {{ $month }})
            </h3>
            <a href="{{ route('payroll.index') }}" class="btn_back_payroll">← Quay lại</a>
        </div>
        <div class="table-responsive box-employees">
            <table class="table table-bordered table-hover-custom text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ngày/Tháng/Năm</th>
                        <th>Giờ Vào</th>
                        <th>Giờ Ra</th>
                        <th>Số Giờ</th>
                        <th>Lương Ca</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $index => $att)
                        <tr>
                            <td class="hover_1">{{ $index + 1 }}</td>
                            <td class="hover_1">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                            <td class="hover_1">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '—' }}
                            </td>
                            <td class="hover_1">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}
                            </td>
                            <td class="hover_1">{{ $att->duration }}h</td>
                            <td class="hover_1">{{ number_format($att->wage) }} $</td>

                            @if ($att->status == 'Hoàn thành')
                                <td class="success_custom">{{ $att->status }}</td>
                            @elseif ($att->status == 'Đang On-Duty')
                                <td class="primary_custom">{{ $att->status }}</td>
                            @elseif(Str::startsWith($att->status, 'Còn'))
                                <td class="continue_custom">{{ $att->status }}</td>
                            @else
                                <td class="hover_1">{{ $att->status }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="hover_1" colspan="6">Không có dữ liệu chấm công trong tháng này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                            <td>{{ number_format($summary->total_wage) }}$</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection