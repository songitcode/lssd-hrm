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
            <p class="text">
                Hiển thị thông tin tiền lương, hệ số, số phút làm việc, tổng kết trước ngày 30
            </p>
        </div>
        <div class="table-responsive box-employees payroll-position-relative">
            <div id="loading-spinner" style="text-align: center; margin: 10px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <table class="table table-bordered table-hover-custom table-employees">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Tên Sĩ Quan</th>
                        <th>Chức Vụ</th>
                        <th>Quân Hàm</th>
                        <th>Phút Làm Việc Trong Tháng</th>
                        <th>Hệ Số Lương</th>
                        <th>Tổng Lương Tháng</th>
                        <th class="text-center">Lịch Sử
                            Chấm Công</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        @php
                            $userID = (int) $user->id;
                            $summary = $summaries[$userID] ?? null;
                            $minutesWorked = $summary ? $summary->total_minutes : 0;
                            $hoursWorked = $summary ? $summary->total_hours : 0;
                            $wage = $summary ? number_format($summary->total_wage) : 0;
                            $rate = $user->effectiveSalaryRate();
                        @endphp
                        <tr>
                            <td class="hover_1 text-center">{{ $index + 1 }}</td>
                            <td class="hover_1">{{ $user->employee->name_ingame ?? $user->username }}</td>
                            <td class="hover_1">{{ $user->employee->position->name_positions ?? '—' }}</td>
                            <td class="hover_1">{{ $user->employee->rank->name_ranks ?? '—' }}</td>
                            <td class="hover_1">{{ $minutesWorked }} phút ~ {{ $hoursWorked }}h</td>
                            <td class="hover_1">{{ number_format($rate) }}$/h</td>
                            <td class="hover_1">{{ $wage }}$</td>
                            <td class="text-center history_function">
                                <a href="{{ route('payroll.user_attendance', $user) }}" class="btn_xem_lich_su_cham_cong"
                                    target="_parent">
                                    Xem <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3 d-flex justify-content-end gap-2">
            <a href="{{--route('payroll.export') --}}" class="btn btn-success">
                📥 Xuất Excel
            </a>
            @if(auth()->user()->role === 'admin')
                <form action="{{ route('attendance.resetAll') }}" method="POST"
                    onsubmit="return confirm('WARNING!! Bạn có chắc chắn muốn xóa toàn bộ dữ liệu chấm công? sẽ không khôi phục được dữ liệu')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="resetButon">
                        <i class="fa fa-trash"></i> Reset Toàn Bộ Dữ Liệu Chấm Công
                    </button>
                </form>
            @endif

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
        </script>
    @endpush