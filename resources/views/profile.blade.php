@extends('layouts.app')

@section('title', 'Hồ Sơ Cá Nhân')
@php
    $highRoles = ['admin', 'thư ký', 'trợ lý cục trưởng', 'phó cục trưởng', 'cục trưởng'];
    $isHighRole = in_array(auth()->user()->role, $highRoles);
@endphp

@push('styles')
    <style>
        .profile-avatar {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 50%;
            /* box-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 14px #e8a800, 0 0 20px #e8a800, 0 0 25px #e8a800, 0 0 30px #e8a800, 0 0 35px #e8a800; */
            animation: glow 2s ease-in-out infinite alternate;
        }

        @-webkit-keyframes glow {
            from {
                box-shadow: 0 0 3px #fff, 0 0 7px #fff, 0 0 14px #e8a800, 0 0 20px #e8a800, 0 0 25px #e8a800, 0 0 30px #e8a800, 0 0 35px #e8a800;
            }

            to {
                box-shadow: 0 0 5px #fff, 0 0 10px #e9ff5b, 0 0 15px #e9ff5b, 0 0 20px #e9ff5b, 0 0 25px #e9ff5b, 0 0 30px #e9ff5b, 0 0 40px #e9ff5b;
            }
        }

        .box-profile {
            border: 1px solid #e8a800;
            border-radius: 20px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
        }

        .btn-update-profile {
            color: #6C757D;
            border-radius: 10px;
            padding: 8px 16px;
            margin: 32px 0 16px;
            font-weight: 300;
            font-size: 16px;
            border: 1px solid #e8a800;
            background-color: none;

            &:hover {
                color: white;
                background: #e8a800
            }
        }
    </style>
@endpush
{{--
@php
$currentUser = auth()->user();
$canEditPosition = $currentUser->canEditPositionOf($employee->user);
@endphp
--}}

@section('content')
    <div class="container py-5">

        <form class="box-profile p-4" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @if (!$employee)
            @else
                <h3 class="mb-4"> Hồ Sơ Sĩ Quan <strong class="text-warning">{{ $employee->name_ingame }}</strong></h3>
            @endif
            @csrf
            @method('PUT')
            {{-- @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @error('avatar')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror --}}

            <div class="row g-4">

                @if ($employee)
                    <div class="col-md-4 text-center mt-5">
                        <img id="avatarPreview"
                            src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : asset('assets/images/user_preview_logo.png') }}"
                            class="profile-avatar mb-3">
                        <div>
                            <input type="file" name="avatar" accept="image/*" class="d-none" id="avatarInput"
                                onchange="previewAvatar(event)">
                            <label for="avatarInput" class="btn btn-outline-primary btn-sm px-3 py-2 mt-4">
                                <i class="bi bi-image"></i> Chọn Ảnh
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control input__view" value="{{ $employee->user->username }}"
                                disabled>
                        </div>

                        @if($canEditPosition)
                            <div class="mb-3">
                                <label class="form-label">Chức vụ</label>
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
                                <label class="form-label">Chức vụ</label>
                                <input type="text"
                                    class="form-control input__view {{ in_array($employee->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'high-level' : '' }}"
                                    value="{{ $employee->position->name_positions }}" disabled>
                            </div>

                        @endif
                        <div class="mb-3">
                            <label class="form-label">Tên Ingame</label>
                            @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng', 'trợ lý cục trưởng']))
                                <input type="text" name="name_ingame" class="form-control input__view"
                                    value="{{ $employee->name_ingame }}" readonly required>
                            @else
                                <input type="text" name="name_ingame" class="form-control input__view"
                                    value="{{ $employee->name_ingame }}"  required>
                            @endif

                        </div>
                        @if(auth()->user()->getRoleLevel() >= 1)
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
                        @else
                            <div class="mb-3">
                                <label class="form-label">Quân hàm</label>
                                <input type="text" class="form-control input__view" value="{{ $employee->rank->name_ranks }}"
                                    disabled>
                            </div>
                        @endif

                        @if (!in_array(auth()->user()->role, ['cục trưởng', 'phó cục trưởng']))
                            <button type="submit" class="btn-update-profile mt-3">Cập nhật ảnh đại diện</button>
                        @else
                            <button type="submit" class="btn-update-profile mt-3">Cập nhật hồ sơ</button>
                        @endif
                        <!-- <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                                                                                                                                                                                    class="btn btn-warning mt-3 ms-2">🔐 Đổi mật khẩu</a> -->
                    </div>
                @else
                    <div class="text-white bg-warning p-3">ADMIN KHÔNG PHẢI HỒ SƠ NHÂN SỰ</div>
                @endif
            </div>
        </form>

        {{-- Đổi mật khẩu (re-use modal)
        @include('partials.change_password_modal', ['userId' => $employee->id])--}}
    </div>
@endsection