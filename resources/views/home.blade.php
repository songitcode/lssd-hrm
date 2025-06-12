@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endpush

@extends('layouts.app')

<!-- @section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Chấm công <span id="currentMonth"></span></h2>
                <div class="text-success fw-bold">Tổng tháng: <span id="totalAmount">0$</span></div>
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" id="checkinBtn">
                    ✓ On-duty
                </button>
                <button class="btn btn-danger" id="checkoutBtn">
                    ✗ Off-duty
                </button>
            </div>
        </div>

        <div class="row row-cols-7 g-2 mb-4" id="calendar"></div>

        <div class="card">
            <div class="card-header">Lịch sử chi tiết</div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tên Sĩ Quan</th>
                        <th>Ngày</th>
                        <th>Phiên làm việc</th>
                        <th>Thời gian</th>
                        <th>Thành tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody"></tbody>
            </table>
        </div>
    </div>
@endsection -->

@section('content')

@endsection

@push('scripts')
    <script>
        document.addEventListener("click", function (e) {
            const popup = document.querySelector(".popup");
            const checkbox = popup.querySelector("input[type=checkbox]");

            if (!popup.contains(e.target)) {
                checkbox.checked = false;
            }
        });
    </script>
@endpush