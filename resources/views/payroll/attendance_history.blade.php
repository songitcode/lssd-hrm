@extends('layouts.app')

@section('title', 'Lịch Sử')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payrol_page.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="group-function">
            <div class="info row">
                <div class="col-lg-5 info-left">
                    <p class="mb-0">Tên Sĩ Quan: <strong>{{ $user->employee->name_ingame ?? $user->username }}
                            ({{ $user->username }})</strong></p>
                    <p class="mb-0">Chức vụ: <strong>{{ $user->employee->position->name_positions }}</strong></p>
                    <p class="mb-0">Quân hàm: <strong>{{ $user->employee->rank->name_ranks }}</strong></p>
                    <p class="mb-0">ID Sĩ Quan: <strong>{{ $user->employee->id }}</strong></p>
                    <p class="mb-0">Tháng: <strong>{{ $month }} (Lương trực tiếp:
                            <strong>{{ number_format($monthlyTotal) }}$</strong>)</strong></p>
                </div>
                <div class="col-lg-5 info-center">
                    <p class="mb-0">Hệ số lương: <strong>{{ number_format($heSoLuong) }}$/1h</strong></p>
                    <p class="mb-0">Sự nghiệp sĩ quan: <strong>{{ number_format($totalLuong) }}$</strong></p>
                    <p class="mb-0">Tính lương: <strong>Giờ làm việc *<span style="font-size: 8px;">(nhân)</span>
                            {{ number_format($heSoLuong) }}$</strong></p>
                </div>
                <div class="col-lg-2 info-right d-flex justify-content-end align-items-center">
                    <a href="{{ route('payroll.index') }}" class="btn_back_payroll">← Quay lại</a>
                </div>
            </div>
        </div>
        <!-- Bảng lương tháng hiện tại -->
        <div class="table-responsive box-employees">
            <table class="tb-timekeeping table-bordered table-hover-custom text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID</th>
                        <th>Ngày/Tháng/Năm</th>
                        <th>Giờ Vào</th>
                        <th>Giờ Ra</th>
                        <th>Số Giờ</th>
                        <th>Lương Ca</th>
                        <th>Trạng Thái</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($currentAttendances as $index => $attendance)
                        <tr>
                            <td class="hover_1">{{ $index + 1 }}</td>
                            <td class="hover_1">{{ $attendance->id }}</td>
                            <td class="hover_1">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                            <td class="hover_1">
                                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s - d/m') : '—' }}
                            </td>
                            <td class="hover_1">
                                {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s - d/m') : '—' }}
                            </td>
                            <!-- <td class="hover_1">{{ number_format($attendance->duration, 2) }}h</td> -->
                            <td class="hover_1">
                                {{ number_format($attendance->duration, 2) }}h
                            </td>
                            <td class="hover_1">
                                {{ number_format($attendance->wage) }} $
                            </td>

                            <td class="hover_1">
                                {{ $attendance->status }}
                            </td>
                            <td class="hover_1_custom">
                                <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST"
                                    class="delete-attendance">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-attendance">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                            {{--@if ($attendance->status == 'Hoàn thành')
                            <td class="success_custom">{{ $attendance->status }}</td>
                            @elseif ($attendance->status == 'Đang On-Duty')
                            <td class="primary_custom text-white">{{ $attendance->status }}</td>
                            @elseif (Str::contains($attendance->status, 'Cảnh Báo'))
                            <td class="danger_custom text-white">{{ $attendance->status }}</td>
                            @elseif (Str::contains($attendance->status, 'TỰ ĐỘNG'))
                            <td class="bg-success text-white">{{ $attendance->status }}</td>
                            @elseif(Str::startsWith($attendance->status, 'Còn'))
                            <td class="continue_custom">{{ $attendance->status }}</td>
                            @else
                            <td class="hover_1">{{ $attendance->status }}</td>
                            @endif--}}
                        </tr>
                    @endforeach
                    @php
                        $totalHours = $currentAttendances->sum('duration');
                        $totalWage = $currentAttendances->sum('wage');
                    @endphp
                    {{-- <tr class="total-day-timekeeping">
                        <td colspan="6">Tổng tháng:
                            {{ $currentAttendances->first() ?
                            \Carbon\Carbon::parse($currentAttendances->first()->date)->format('m/Y') : '' }}
                        </td>
                        <td>Tổng Giờ: {{ number_format($totalHours, 2) }}h</td>
                        <td colspan="2">Tổng Lương: {{ number_format($totalWage) }}$</td>
                    </tr> --}}
                    <tr class="total-day-timekeeping">
                        <td colspan="6">Tổng tháng:
                            {{ $currentAttendances->first() ? \Carbon\Carbon::parse($currentAttendances->first()->date)->format('m/Y') : '' }}
                        </td>
                        <td>Tổng Giờ: <span id="month-total-hours">{{ number_format($totalHours, 2) }}</span>h</td>
                        <td colspan="2">Tổng Lương: <span id="month-total-wage">{{ number_format($totalWage) }}</span>$</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--  -->
        <div class="group-function">
            <h3>Chỉnh Sửa Bảng Lương</h3>
            <div class="info row">
                <div class="col-lg-5 info-left">
                    <p>Lịch sử sự nghiệp của sĩ quan <strong>{{ $user->employee->name_ingame ?? $user->username }}
                            ({{ $user->username }})</strong></p>
                </div>
                <div class="col-lg-5 info-center">
                    <p class="mb-0">Sự nghiệp sĩ quan: <strong>{{ number_format($totalLuong) }}$</strong></p>
                </div>
            </div>
        </div>
        <!-- Bảng lương tất cả tháng hiện có -->
        <div class="table-responsive box-employees">
            <table class="tb-timekeeping table-bordered table-hover-custom text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID</th>
                        <th>Ngày/Tháng/Năm</th>
                        <th>Giờ Vào</th>
                        <th>Giờ Ra</th>
                        <th>Số Giờ</th>
                        <th>Lương Ca</th>
                        <th>Trạng Thái</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Nhóm theo tháng trước
                        $groupedByMonth = $attendancesAllMonthly->groupBy(function ($att) {
                            return \Carbon\Carbon::parse($att->date)->format('m/Y');
                        });
                    @endphp

                    @foreach($groupedByMonth as $month => $monthlyAttendances)
                        <!-- Hàng tiêu đề tháng  -->
                        <tr class="bg_secondary_cus p-0">
                            <td colspan="9" class="text-center">
                                <strong>Bảng chấm công tháng {{ $month }}</strong>
                            </td>
                        </tr>

                        @php
                            // Nhóm tiếp theo ngày trong tháng
                            $groupedAttendances = $monthlyAttendances->groupBy('date');
                            $totalMonthHours = $monthlyAttendances->sum('duration');
                            $totalMonthWage = $monthlyAttendances->sum('wage');
                            $stt = 1;
                        @endphp

                        @foreach($groupedAttendances as $date => $records)
                            @foreach($records as $index => $att)
                                <tr>
                                    <td class="hover_1">{{ $stt++}}</td>
                                    <td class="hover_1">{{ $att->id }}</td>
                                    <td class="hover_1">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                                    <td class="hover_1">
                                        {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s - d/m') : '—' }}
                                    </td>
                                    <td class="hover_1">
                                        {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i:s - d/m') : '—' }}
                                    </td>
                                    <td class="hover_1 editable" data-id="{{ $att->id }}" data-field="duration" contenteditable="true">
                                        {{ number_format($att->duration, 2) }}h
                                    </td>
                                    <td class="hover_1 editable" data-id="{{ $att->id }}" data-field="wage" contenteditable="true">
                                        {{ number_format($att->wage) }} $
                                    </td>
                                    @if ($att->status == 'Hoàn thành')
                                        <td class="success_custom editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @elseif ($att->status == 'Đang On-Duty')
                                        <td class="primary_custom text-white editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @elseif (Str::contains($att->status, 'Quản Lý'))
                                        <td class="danger_custom text-white editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @elseif (Str::contains($att->status, 'Tự Động'))
                                        <td class="bg-success text-white editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @elseif(Str::contains($att->status, 'Dư'))
                                        <td class="bg-danger text-white editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @else
                                        <td class="hover_1 editable" data-id="{{ $att->id }}" data-field="status" contenteditable="true">{{ $att->status }}</td>
                                    @endif
                                    <td class="hover_1_custom">
                                        <form action="{{ route('attendance.destroy', $att->id) }}" method="POST"
                                            class="delete-attendance">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-attendance">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            {{--
                            @php
                            $totalHours = $records->sum('duration');
                            $totalWage = $records->sum('wage');
                            @endphp
                            <tr class="total-day-timekeeping">
                                <td colspan="5">Tổng ngày:
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                </td>
                                <td>Tổng Giờ: {{ number_format($totalHours, 2) }}h</td>
                                <td colspan="2">Tổng Lương: {{ number_format($totalWage) }}$</td>
                            </tr>--}}
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3 phan_trang">
                {{ $attendancesAllMonthly->links() }}
            </div>
        </div>
        @if ($monthlySummaries->isNotEmpty())
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
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelector('.btn_back_payroll').addEventListener('submit', function (e) {
            showLoading();
        });
        //// Xác nhận xóa với SweetAlert2
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-attendance').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // chặn submit gốc

                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: "Cột này sẽ bị xóa và không thể khôi phục!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Vâng, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // cho submit thật
                        }
                    });
                });
            });
        });
        //// Ajax inline edit
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.editable').forEach(cell => {
                cell.addEventListener('blur', function () {
                    let id = this.dataset.id;
                    let field = this.dataset.field;
                    let value = this.innerText.trim();
                    let cellElement = this;

                    if (!value) return; // tránh gửi rỗng

                    Swal.fire({
                        title: 'Xác nhận lưu thay đổi?',
                        text: `Bạn có chắc chắn muốn cập nhật thành "${value}" không?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Có, lưu lại',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/payroll/user/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ field, value })
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        cellElement.style.backgroundColor = "#92ffabff";
                                        Swal.fire('Thành công', 'Dữ liệu đã được cập nhật!', 'success');
                                    } else {
                                        cellElement.style.backgroundColor = "#f82637ff";
                                        Swal.fire('Lỗi', 'Cập nhật thất bại.', 'error');
                                    }
                                    setTimeout(() => cellElement.style.backgroundColor = "", 1000);
                                })
                                .then(({ success, summary }) => {
                                    if (success && summary) {
                                        document.getElementById('month-total-hours').textContent = summary.total_hours_formatted;
                                        document.getElementById('month-total-wage').textContent = summary.total_wage_formatted;
                                    }
                                })
                                .catch(err => console.error(err));
                        } else {
                            // Nếu hủy thì reload lại giá trị cũ (optional)
                            cellElement.textContent = cellElement.getAttribute('data-old') || '';
                        }
                    });
                });

                // Lưu giá trị ban đầu để khôi phục nếu user cancel
                cell.addEventListener('focus', function () {
                    this.setAttribute('data-old', this.innerText.trim());
                });
            });
        });
    </script>
@endpush