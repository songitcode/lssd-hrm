@extends('layouts.app')

@section('title', 'Lương')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payrol_page2.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
@endpush

@section('content')
    <div class="pr-page mt-5">
        <div class="pr-wrap">

            {{-- ── TOP BAR ── --}}
            <div class="pr-topbar">
                <div class="pr-topbar-title">
                    <h2>Thông Tin Chấm Công &amp; Tiền Lương</h2>
                    <span class="pr-month-tag">Tháng {{ $currentMonth }}</span>
                </div>
                <!-- <div class="pr-search-wrap">
                            <i class="fas fa-search pr-search-ico"></i>
                            <input type="text" id="search-employee" class="pr-search-input"
                                placeholder="Tìm tên sĩ quan hoặc tên đăng nhập...">
                        </div> -->
            </div>

            {{-- ── STAT CARDS ── --}}
            <div class="pr-stats">
                <div class="pr-stat-card pr-stat--amber">
                    <span class="pr-stat-label">Tổng lương tháng {{ $currentMonth }}</span>
                    <span class="pr-stat-value pr-v--amber">{{ number_format($tongTienLuongThang) }}$</span>
                </div>
                <div class="pr-stat-card pr-stat--blue">
                    <span class="pr-stat-label">Tổng nhân viên</span>
                    <span class="pr-stat-value pr-v--blue">{{ $tongNhanVien }}</span>
                </div>
                <div class="pr-stat-card pr-stat--green">
                    <span class="pr-stat-label">Đã chấm công</span>
                    <span class="pr-stat-value pr-v--green">{{ $tongNhanVienDaChamCong }}</span>
                </div>
            </div>

            {{-- ── TABLE CARD ── --}}
            <div class="pr-table-card">

                {{-- Toolbar --}}
                <div class="pr-toolbar">
                    <div class="pr-search-wrap">
                        <i class="fas fa-search pr-search-ico"></i>
                        <input type="text" id="search-employee" class="pr-search-input"
                            placeholder="Tìm tên sĩ quan hoặc tên đăng nhập...">
                    </div>
                    @if(auth()->user()->isDownAdminRole())
                        <form action="{{ route('attendance.resetAttendanceDta') }}" method="POST"
                            onsubmit="return confirm('WARNING!! Bạn có chắc chắn muốn xóa toàn bộ dữ liệu chấm công? sẽ không khôi phục được dữ liệu')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pr-btn pr-btn--red-outline" id="resetButon">
                                <i class="fa fa-trash"></i> Reset Toàn Bộ Dữ Liệu
                            </button>
                        </form>
                    @endif
                    <button class="pr-btn pr-btn--green"
                        onclick="exportTableToExcel('payrollTable', 'bang-luong-thang-{{ $currentMonth }}')">
                        <i class="fa fa-file-excel"></i>
                        Xuất Excel Tháng {{ $currentMonth }}
                    </button>
                    <button class="pr-btn pr-btn--blue" id="viewPrevPayroll">
                        <i class="fa-solid fa-chart-column"></i>
                        Bảng Lương Tháng {{ now()->subMonth()->month }}
                    </button>
                </div>

                {{-- Loading overlay --}}
                <div id="loading-spinner" class="pr-spinner-wrap">
                    <div class="pr-spinner"></div>
                </div>

                {{-- Table --}}
                <div class="pr-tbl-wrap">
                    <table id="payrollTable" class="pr-tbl table-bordered">
                        <thead>
                            <tr>
                                <th class="pr-th--center">STT</th>
                                <th>Tên Sĩ Quan</th>
                                <th>Chức Vụ</th>
                                <th>Quân Hàm</th>
                                <th>Phút ~ Giờ Làm Việc</th>
                                <th>Hệ Số Lương</th>
                                <th>Tổng Lương Tháng</th>
                                <th class="pr-th--center">Lịch Sử Chấm Công</th>
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
                                    <td class="pr-td pr-td--stt">{{ $stt++ }}</td>
                                    <td class="pr-td pr-td--name">{{ $user->employee->name_ingame ?? $user->username }}</td>
                                    <td class="pr-td">{{ $user->employee->position->name_positions ?? '—' }}</td>
                                    <td class="pr-td">{{ $user->employee->rank->name_ranks ?? '—' }}</td>
                                    <td class="pr-td pr-td--num">{{ $minutesWorked }} phút ~ {{ $hoursWorked }}h</td>
                                    <td class="pr-td pr-td--rate">{{ number_format($rate) }}$/h</td>
                                    <td class="pr-td pr-td--wage">{{ $wage }}$</td>
                                    <td class="pr-td-action">
                                        <a href="{{ route('payroll.user_attendance', $user) }}" target="_parent"
                                            class="pr-btn-view btn_xem_lich_su_cham_cong">
                                            Xem <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── MODAL bảng lương tháng trước ── --}}
            <div class="modal fade" id="previousPayrollModal" tabindex="-1" aria-labelledby="previousPayrollLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content pr-modal-content">
                        <div class="modal-header pr-modal-header">
                            <h5 class="modal-title" id="previousPayrollLabel">
                                Bảng Lương Tháng {{ now()->subMonth()->month }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <p class="pr-modal-subtitle">Xem lại bảng lương tháng trước để dễ dàng thống kê</p>
                        <div class="modal-body" id="prevPayrollContent">
                            <div style="text-align:center;padding:2rem;">
                                <div class="pr-spinner" style="margin:auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/payroll.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hiển thị loader khi click xem lịch sử
            document.querySelectorAll('.btn_xem_lich_su_cham_cong').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('loadingOverlay').style.display = 'flex';
                });
            });
        });

        // Xóa dữ liệu bảng lương tháng trước
        document.getElementById('deletePrevPayrollForm')?.addEventListener('submit', function (e) {
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
                            document.querySelector('#previousPayrollModal .modal-body').innerHTML =
                                '<p style="color:#059669">Đã xóa thành công.</p>';
                        });
                }
            });
        });
    </script>

    {{-- Script xuất Excel --}}
    <script>
        function exportTableToExcel(tableId, filename) {
            filename = filename || 'data.xlsx';
            var table = document.getElementById(tableId);
            if (!table) { alert('Không tìm thấy bảng với ID: ' + tableId); return; }

            var wb = XLSX.utils.book_new();
            var data = [];

            var headers = [];
            table.querySelectorAll('thead tr th').forEach(function (th) {
                headers.push(th.innerText.trim());
            });
            data.push(headers);

            table.querySelectorAll('tbody tr').forEach(function (tr) {
                var row = [];
                tr.querySelectorAll('td').forEach(function (td) { row.push(td.innerText.trim()); });
                if (row.length > 0) data.push(row);
            });

            var ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, filename + '.xlsx');
        }
    </script>
@endpush