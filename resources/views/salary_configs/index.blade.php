@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush
@section('content')
    <div class="container">
        @if (in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'admin']))
            <div class="group-function p-3">
                <form method="POST" action="{{ route('salary_configs.store') }}" class="row g-3 form-salary">
                    @csrf
                    {{--<div class="col-md-4">
                        <select name="position_id" class="form-select" required>
                            <option value="">--- Chọn chức vụ ---</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name_positions }}</option>
                            @endforeach
                        </select>
                    </div>--}}
                    <div class="col-md-4">
                        <select name="rank_id" class="form-select" required>
                            <option value="">--- Chọn quân hàm ---</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name_ranks }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="hourly_rate_display" id="hourly_rate_display" class="form-control"
                            placeholder="Lương/giờ" required>
                        <input type="hidden" name="hourly_rate" id="hourly_rate" max="7">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary">Cập nhật hệ số</button>
                    </div>
                </form>
                <!-- Form sửa giờ làm tối đa cho toàn hệ thống -->
                <form method="POST" action="{{ route('salary_configs.updateGlobalHours') }}"
                    class="row g-3 form-edit-time mt-2">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label for="max_hours_per_day">Giờ làm tối đa/ngày (toàn hệ thống):</label>
                        <input type="number" step="0.1" min="0" max="24" name="max_hours_per_day" id="max_hours_per_day"
                            class="form-control" value="{{ $configs->first()?->max_hours_per_day ?? 3 }}" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-warning mt-4">Cập nhật giờ làm</button>
                    </div>
                </form>
            </div>
        @else
            <div class="group-function p-3">
                Không có thẩm quyền để cập nhật hệ số lương.
            </div>
        @endif
        <div class="box-employees table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Chức vụ</th>
                        <th>Lương/Giờ</th>
                        <th>Giờ tối đa</th>
                        <th>Người cập nhật</th>
                        <th>Thời gian thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($configs as $cfg)
                        <tr>
                            <td>{{ $cfg->rank->name_ranks }}</td>
                            <td>{{ number_format($cfg->hourly_rate) }}$</td>
                            <td>{{ $cfg->max_hours_per_day }}h</td>
                            <td>{{ $cfg->updatedBy?->username ?? 'Không rõ' }}</td>
                            <td>{{ $cfg->updated_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const displayInput = document.getElementById('hourly_rate_display');
            const hiddenInput = document.getElementById('hourly_rate');

            displayInput.addEventListener('input', function () {
                // Lấy số gốc không chứa dấu phẩy
                let raw = displayInput.value.replace(/[^0-9]/g, '');

                // Gán vào input ẩn
                hiddenInput.value = raw;

                // Format lại hiển thị có dấu phẩy
                if (raw) {
                    displayInput.value = Number(raw).toLocaleString('en-US');
                } else {
                    displayInput.value = '';
                }
            });
        });
        document.querySelector('.form-edit-time').addEventListener('submit', function (e) {
            showLoading();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const salaryForm = document.querySelectorAll('.form-salary');
            salaryForm.forEach(form => {
                form.addEventListener('submit', function () {
                    showLoading();
                });
            });
        });
    </script>

@endpush