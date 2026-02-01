@extends('layouts.app')

@section('title', 'Lương')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payrol_page.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="group-function row">
            <div class="col-lg-6">
                <h3>Thông Tin Chấm Công Và Tiền Lương - <span class="h6">Tháng {{ $currentMonth }}</span></h3>
            </div>
            <div class="col-lg-6">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search-employee" class="form-control search-input"
                        placeholder="Tìm tên sĩ quan hoặc tên đăng nhập...">
                </div>
            </div>
            <div class="info-wage d-flex gap-1 fst-italic">
                <p class="info">
                    Tổng tiền lương tháng
                    <strong>{{ $currentMonth }}</strong>:
                    <strong class="text-success">{{ number_format($tongTienLuongThang) }}$</strong>
                </p>
                <span>
                    Tổng nhân viên : <strong class="text-primary">{{ $tongNhanVien }}</strong> -
                    Nhân viên đã chấm công : <strong class="text-primary">{{ $tongNhanVienDaChamCong }}</strong>
                </span>
            </div>
        </div>
        <div class="table-responsive box-employees payroll-position-relative">
            <div id="loading-spinner" style="text-align: center; margin: 10px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="d-flex justify-content-end p-3 gap-2">
                @if(auth()->user()->isDownAdminRole())
                    <form action="{{ route('attendance.resetAttendanceDta') }}" method="POST"
                        onsubmit="return confirm('WARNING!! Bạn có chắc chắn muốn xóa toàn bộ dữ liệu chấm công? sẽ không khôi phục được dữ liệu')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-bottom-payroll-2" id="resetButon">
                            <i class="fa fa-trash"></i> Reset Toàn Bộ Dữ Liệu Chấm Công
                        </button>
                    </form>
                @endif
                <button class="btn btn-success"
                    onclick="exportTableToExcel('payrollTable', 'bang-luong-thang-{{ $currentMonth }}')">
                    <i class="fa fa-file-excel"></i>
                    Xuất Excel Tháng {{ $currentMonth }}
                </button>
                <button class="btn btn-primary" id="viewPrevPayroll">
                    <i class="fa-solid fa-chart-column"></i>
                    Bảng Lương Tháng {{ now()->subMonth()->month }}
                </button>
            </div>
            <!-- Bảng -->
            <table id="payrollTable" class="table table-bordered table-hover-custom table-employees">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Tên Sĩ Quan</th>
                        <th>Chức Vụ</th>
                        <th>Quân Hàm</th>
                        <th>Phút ~ Giờ Làm Việc Trong Tháng</th>
                        <th>Hệ Số Lương</th>
                        <th>Tổng Lương Tháng</th>
                        <th class="text-center">Lịch Sử Chấm Công</th>
                    </tr>
                </thead>
                <tbody>
                    @php $stt = 1; @endphp
                    @foreach ($users as $user)
                        @php
                            $positionName = $user->employee->position->name_positions ?? '';
                            $excludePositions = ['Cục Trưởng', 'Phó Cục Trưởng'];

                            if (in_array($positionName, $excludePositions)) {
                                continue;
                            }

                            $userID = (int) $user->id;
                            $summary = $summaries[$userID] ?? null;
                            $minutesWorked = $summary ? $summary->total_minutes : 0;
                            $hoursWorked = $summary ? $summary->total_hours : 0;
                            $wage = $summary ? number_format($summary->total_wage) : 0;
                            $rate = $user->effectiveSalaryRate();
                        @endphp
                        <tr>
                            <td class="hover_1 text-center">{{ $stt++ }}</td>
                            <td class="hover_1">{{ $user->employee->name_ingame ?? $user->username }}</td>
                            <td class="hover_1">{{ $user->employee->position->name_positions ?? '—' }}</td>
                            <td class="hover_1">{{ $user->employee->rank->name_ranks ?? '—' }}</td>
                            <td class="hover_1">{{ $minutesWorked }} phút ~ {{ $hoursWorked }}h</td>
                            <td class="hover_1">{{ number_format($rate) }}$/h</td>
                            <td class="hover_1">{{ $wage }}$</td>
                            <td class="text-center history_function">
                                <a href="{{ route('payroll.user_attendance', $user) }}" target="_parent"
                                    class="btn_xem_lich_su_cham_cong">
                                    Xem <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3 d-flex justify-content-end gap-2">
            <!-- MODAL Xem bản lương tháng trước -->
            <div class="modal fade" id="previousPayrollModal" tabindex="-1" aria-labelledby="previousPayrollLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Bảng Lương Tháng {{ now()->subMonth()->month }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <p class="text text-start ms-3 p-0">Xem lại bảng lương tháng trước để dễ dàng thống kê </p>
                        <div class="modal-body" id="prevPayrollContent">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                        {{--
                        <div class="modal-footer">
                            <form id="deletePrevPayrollForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa Bảng Lương Tháng
                                    {{ now()->subMonth()->month }}</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                        --}}
                    </div>
                </div>
            </div>
        </div>
@endsection
    @push('scripts')
        <script src="{{ asset('assets/js/payroll.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const xemBtns = document.querySelectorAll('.btn_xem_lich_su_cham_cong');

                xemBtns.forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        // Hiển thị loader
                        document.getElementById('loadingOverlay').style.display = 'flex';
                    });
                });
            });

            // Xóa dữ liệu bảng lương tháng trước
            document.getElementById('deletePrevPayrollForm').addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Bạn có chắc muốn xóa?',
                    text: 'Toàn bộ bảng lương tháng trước sẽ bị xóa!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Vâng, xóa đi!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('payroll.previous.delete') }}", {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa bảng lương thành công',
                                    text: data.message,
                                    confirmButtonText: 'Đã hiểu',
                                    confirmButtonColor: '#3085d6'
                                });
                                document.getElementById('previousPayrollModal').querySelector('.modal-body').innerHTML = '<p class="text-success">Đã xóa thành công.</p>';
                            });
                    }
                });
            });
            ////
        </script>
        <!-- Script xuất Excel -->
        <script>
            function exportTableToExcel(tableId, filename = 'data.xlsx') {
                let table = document.getElementById(tableId);
                if (!table) {
                    alert('Không tìm thấy bảng với ID: ' + tableId);
                    return;
                }

                let wb = XLSX.utils.book_new();
                let data = [];

                // Lấy header
                let headers = [];
                table.querySelectorAll('thead tr th').forEach(th => {
                    headers.push(th.innerText.trim());
                });
                data.push(headers);

                // Lấy body
                table.querySelectorAll('tbody tr').forEach(tr => {
                    let row = [];
                    tr.querySelectorAll('td').forEach(td => {
                        row.push(td.innerText.trim());
                    });
                    if (row.length > 0) {
                        data.push(row);
                    }
                });

                // Chuyển thành sheet
                let ws = XLSX.utils.aoa_to_sheet(data);

                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, filename + '.xlsx');
            }
        </script>
    @endpush