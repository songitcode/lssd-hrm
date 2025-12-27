@extends('layouts.app')

@section('title', 'Đơn Xin Nghỉ Phép')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/take_leave.css') }}">
@endpush

@section('content')
    <form class="box-form-1 container mb-5 mt-5">
        <h2 class="text-color-efb036">Đơn Xin Nghỉ Phép</h2>
        <hr class="text-color-efb036">
        <!--  -->
        <div class="form-group-1">
            <label id="label-1">Kính Gửi Cục Trưởng</label>
        </div>
        <div class="form-group-1">
            <p></p>
            <select id="select-1">
                <option value="Đơn Xin Nghỉ Phép">Đơn Xin Nghỉ Phép</option>
                <option value="Đơn Xin Nghỉ Phép Dài Hạn">Đơn Xin Nghỉ Phép Dài Hạn</option>
                <option value="Đơn Xin Rời Ngành">Đơn Xin Rời Ngành</option>
            </select>
        </div>
        <div class="form-group-1">
            <label id="label-2">Cục Trưởng:</label>
            <li id="quan-li-1" value="<@167389650581716992>" class="input-ia static-input">
                <b class="text-danger">Super Dope</b>
            </li>
        </div>
        <div class="form-group-1">
            <label id="label-3">Phó Cục Trưởng:</label>
            <li id="quan-li-2" class="input-ia static-input" value="<@604610757656576000>"><span style="color: #BF3131;">Duy Iress</span></li>
        </div>
        <div class="form-group-1">
            <label id="label-4">Trợ Lý Điều Hành:</label>
            <li id="quan-li-3" class="input-ia static-input" value="<@1171037567630712915>">
                <span style="color: #BF3131;">Phuong Teddy</span>
            </li>
        </div>
        <div class="form-group-1">
            <label id="label-5" class="label-select">Đội Trưởng:</label>
            <select id="select-2">
                <option value="">--- Không Có Đội Trưởng ---</option>
                <option value="<@489780355453288449>">GS00 | Hvien Dat</option>
                <option value="<@440837500848570376>">DS00 | Son Myname</option>
                <option value="<@547944496038543370>">SS00 | Im Bill</option>
            </select>
        </div>
        <div class="form-group-1">
            <label id="label-6" class="label-select">Đội Phó:</label>
            <select id="select-3">
                <option value="">--- Không Có Đội Phó ---</option>
                <option value="<@1366784682099740814>">SS | D. Garp</option>
                <option value="<@1208265190144213026>">DS | Benzily Vy</option>
            </select>
        </div>
        <div class="form-group-1">
            <label id="label-7">Tôi:</label>
            <input class="input-ia" type="text" id="input-1" value="" placeholder="Tên ingame . . ." required>
        </div>
        <div class="form-group-1">
            <label id="label-8" class="label-select">Chức Vụ:</label>
            <select id="select-4">
                <option value="Sĩ Quan Thực Tập">Sĩ Quan Thực Tập</option>
                <option value="Sĩ Quan">Cảnh Sát Viên</option>
                <option value="Đội Phó">Đội Phó</option>
                <option value="Đội Trưởng">Đội Trưởng</option>
                <option value="Thư Ký">Thư Ký</option>
                <option value="Trợ Lý">Trợ Lý</option>
            </select>
        </div>
        <div class="form-group-1">
            <label id="label-9">Số Hiệu:</label>
            <input placeholder="DS11, PS22, SS05, . . ." class="input-ia" type="text" id="input-2" value="" required>
        </div>
        <div class="form-group-1">
            <label id="label-10" class="label-select">Quân Hàm:</label>
            <select id="select-5">
                <option value="Hạ Sĩ">Hạ Sĩ</option>
                <option value="Trung Sĩ">Trung Sĩ</option>
                <option value="Thượng Sĩ">Thượng Sĩ</option>
                <option value="Thiếu Úy">Thiếu Úy</option>
                <option value="Trung Úy">Trung Úy</option>
                <option value="Thượng Úy">Thượng Úy</option>
                <option value="Đại Úy">Đại Úy</option>
                <option value="Thiếu Tá">Thiếu Tá</option>
                <option value="Trung Tá">Trung Tá</option>
                <option value="Thượng Tá">Thượng Tá</option>
                <option value="Đại Tá">Đại Tá</option>
            </select>
        </div>
        <div class="form-group-1">
            <label id="label-11">Lý Do:</label>
            <input placeholder="Lý do cụ thể" class="input-ia" type="text" id="input-3" value="" required>
        </div>
        <div class="form-group-1">
            <p class="goi-y"><b>Gợi Ý</b></p>
            <div class="suggestions" id="suggestions">
                <span class="suggestion-item" onclick="selectSuggestion(this, 'input-3')">Bận Việc OOC</span>
            </div>
        </div>
        <!--  -->
        <div class="form-group-1">
            <label id="label-12">Ngày Xin Nghỉ:</label>
            <input class="input-ia input-date" type="date" id="input-4" value="" required>
        </div>
        <div class="form-group-1">
            <label id="label-13">Ngày Quay Lại:</label>
            <input class="input-ia input-date" type="date" id="input-5" value="" required>
        </div>
        <div class="form-group-1">
            <label id="label-14">
                Ký Tên:</label>
            <input placeholder="Viết lại tên trong game Hoặc tự tag tên của bạn trên discord. . ." class="input-ia" type="text" id="input-6" value="" required>
        </div>
        <hr>
        <div class="d-flex box-button-end">
            <button class="copy-btn" onclick="copyAllToClipboard()">Sao Chép</button>
            <button class="reset-btn" onclick="resetValue()">Reset</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/take_leave.js') }}"></script>
@endpush