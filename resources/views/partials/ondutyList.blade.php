@extends('layouts.app')

@section('title', 'Live Onduty')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/onduty_page.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="group-function row">
            <div class="col-lg-6">
                <h3 class="mb-4">Danh sách On-Duty Trực Tiếp</h3>
            </div>
            <div class="col-lg-6">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control search-input" id="employeeSearchInput"
                        placeholder="Tìm kiếm ...">
                </div>
                {{-- <div id="searchResults" class="list-group position-absolute w-100 mt-1 shadow-sm"
                    style="z-index: 9999; display: none;"></div> --}}
            </div>
        </div>
        <div class="table-responsive box-employees text-center">
            <table class="table table-bordered table-hover-custom">
                <thead>
                    <tr>
                        <th>Tên Ingame</th>
                        <th>Chức vụ</th>
                        <th>Quân hàm</th>
                        <th>Giờ bắt đầu</th>
                        <th>Trạng thái</th>
                        <th>Hồ Sơ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($onDutyList as $att)
                        <tr>
                            <td>{{ $att->user->employee->name_ingame ?? '-' }}</td>
                            <td>{{ $att->user->employee->position->name_positions ?? '-' }}</td>
                            <td>{{ $att->user->employee->rank->name_ranks ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($att->check_in)->format('H:i d/m/Y') }}</td>
                            <td><span class="badge bg-success">{{ $att->status }}</span></td>
                            <td class="profile_function">
                                <button class="btn_xem_ho_so" data-bs-toggle="modal" data-bs-target="#profileModal"
                                    data-name="{{ $att->user->employee->name_ingame }}"
                                    data-position="{{ $att->user->employee->position->name_positions ?? '-' }}"
                                    data-rank="{{ $att->user->employee->rank->name_ranks ?? '-' }}"
                                    data-username="{{ $att->user->username }}" data-status="{{ $att->status }}">
                                    Xem</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Không có ai đang On-Duty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hồ sơ người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Tên Ingame:</strong> <span id="modalName"></span></p>
                    <p><strong>Chức vụ:</strong> <span id="modalPosition"></span></p>
                    <p><strong>Quân hàm:</strong> <span id="modalRank"></span></p>
                    <p><strong>Tài khoản:</strong> <span id="modalUsername"></span></p>
                    <p><strong>Trạng Thái:</strong> <span id="modalStatus"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('profileModal');
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('modalName').textContent = button.getAttribute('data-name');
                document.getElementById('modalPosition').textContent = button.getAttribute('data-position');
                document.getElementById('modalRank').textContent = button.getAttribute('data-rank');
                document.getElementById('modalUsername').textContent = button.getAttribute('data-username');
                document.getElementById('modalStatus').textContent = button.getAttribute('data-status');
            });
        });
    </script>
@endsection