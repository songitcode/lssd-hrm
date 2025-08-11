@extends('layouts.app')

@section('title', auth()->user()->employee->name_ingame ?? 'ADMIN')

@section('title', 'Hồ Sơ Cá Nhân')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush
@php
    $highRoles = ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'];
    $isHighRole = in_array(auth()->user()->role, $highRoles);
@endphp

{{--
@php
$currentUser = auth()->user();
$canEditPosition = $currentUser->canEditPositionOf($employee->user);
@endphp
--}}

@section('content')
    <div class="container py-5 profile-card">
        <form id="deleteAvatarForm" action="{{ route('profile.deleteAvatar') }}" method="POST" class="delete-avatar d-none">
            @csrf
            @method('DELETE')
        </form>

        <form class="box-profile p-4 loader" method="POST" action="{{ route('profile.update') }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row p-2 profile-card">
                @if ($employee)
                    <div class="col-md-4 text-center mt-5 align-items-center ">
                        <div class="avatar-circle">
                            <div class="profile-avatar-wrapper">
                                <img id="avatarPreview"
                                    src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : asset('assets/images/user_preview_logo.png') }}"
                                    class="profile-avatar mb-3">
                            </div>
                        </div>
                        <div>
                            <input type="file" name="avatar" accept="image/*" class="d-none" id="avatarInput"
                                onchange="previewAvatar(event)">
                            <label for="avatarInput" class="btn-change-img mt-4">
                                <i class="fa fa-image"></i> Chọn ảnh đại diện
                            </label>
                        </div>
                        @if ($employee->avatar)
                            <button type="button" onclick="confirmDeleteAvatar()" class="btn-remove-avt">Xoá ảnh đại diện</button>
                        @endif
                    </div>
                    <div class="col-md-8 ">
                        <h3 class="mb-4"> Hồ Sơ Sĩ Quan <strong class="text-warning">{{ $employee->name_ingame }}</strong></h3>

                        <div class="mb-3">
                            <label class="form-label"><b>Tên đăng nhập</b> (Username)</label>
                            <input type="text" class="form-control input__view cursor_not_allowed"
                                value="{{ $employee->user->username }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><b>Tên trong game GTA5VN</b> (Name in game GTA5VN)</label>
                            <br>
                            @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'trợ lý cục trưởng']))
                                <input type="text" name="name_ingame" class="input__view not_allowed"
                                    value="{{ $employee->name_ingame }}" readonly required>
                                {{--<p>{{ $employee->name_ingame }}</p>--}}
                            @else
                                <input type="text" name="name_ingame" class="form-control input__view"
                                    value="{{ $employee->name_ingame }}" required>
                            @endif
                        </div>
                        <label class="form-label"><b>Chức vụ</b> (Position)</label>
                        @if($canEditPosition)
                            <div class="mb-3">
                                <select class="form-select" name="position_id" required>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                            {{ $pos->name_positions }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{--
                            <div class="mb-3">
                                <label class="form-label">Quân hàm</label>
                                <select class="form-select" name="rank_id" required>
                                    @foreach($ranks as $rank)
                                    <option value="{{ $rank->id }}" {{ $employee->rank_id == $rank->id ? 'selected' : '' }}>
                                        {{ $rank->name_ranks }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            --}}
                        @else
                            <div class="mb-3">
                                <div class="position-cus cursor_not_allowed">
                                    <video autoplay muted loop playsinline class="bg-video-position">
                                        <source
                                            src="https://cdn.discordapp.com/assets/collectibles/nameplates/chance/d20_roll/asset.webm"
                                            type="video/webm">
                                        <source
                                            src="https://cdn.discordapp.com/assets/collectibles/nameplates/chance/d20_roll/asset.webm"
                                            type="video/mp4">
                                        {{--
                                        <source
                                            src="https://cdn.discordapp.com/assets/collectibles/nameplates/paper/skibidi_toilet/asset.webm"
                                            type="video/webm">
                                        <source
                                            src="https://cdn.discordapp.com/assets/collectibles/nameplates/paper/skibidi_toilet/asset.webm"
                                            type="video/mp4">
                                        --}}
                                        Your browser does not support the video tag. (Browser của bạn không hỗ trợ video này)
                                    </video>
                                    <input type="text"
                                        class="form-control input__view {{ in_array($employee->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'high-level' : '' }}"
                                        value="{{ $employee->position->name_positions }}" disabled>
                                </div>
                            </div>
                        @endif
                        <label class="form-label"><b>Quân hàm</b> (Military rank)</label>
                        @if(auth()->user()->getRoleLevel() >= 1)
                            <div class="mb-3">
                                <select class="form-select" name="rank_id" required>
                                    @foreach($ranks as $rank)
                                        <option value="{{ $rank->id }}" {{ $employee->rank_id == $rank->id ? 'selected' : '' }}>
                                            {{ $rank->name_ranks }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="mb-3">
                                <input type="text" class="form-control input__view cursor_not_allowed"
                                    value="{{ $employee->rank->name_ranks }}" disabled>
                            </div>
                        @endif
                        @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng']))
                            <button type="submit" class="btn-update-profile mt-3">Cập nhật ảnh đại diện</button>
                        @else
                            <button type="submit" class="btn-update-profile mt-3">Cập nhật hồ sơ</button>
                        @endif
                        {{--
                        <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal" class=" btn-g mt-ms-2">Đổi
                            khẩu</a>
                        --}}
                    </div>
                @else
                    <div class="text-white bg-warning p-3" style="border-radius: 10px;">
                        <p>{{ auth()->user()->id }} </p>
                        <p>{{ auth()->user()->username }} </p>
                    </div>
                @endif
            </div>
        </form>
        {{-- Đổi mật khẩu (re-use modal)
        @include('partials.change_password_modal', ['userId' => $employee->id])--}}
    </div>
@endsection
@push('scripts')
    <script>
        function confirmDeleteAvatar() {
            Swal.fire({
                title: 'Bạn chắc chắn?',
                text: "Xóa ảnh đại diện sẽ không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Huỷ'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    document.getElementById('deleteAvatarForm').submit();
                }
            });
        }

        document.querySelector('.delete-avatar').addEventListener('submit', function (e) {
            showLoading();
        });
    </script>
@endpush