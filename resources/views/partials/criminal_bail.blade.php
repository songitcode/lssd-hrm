@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/form_criminal_bail.css') }}">
@endpush
@section('content')
<div class="container">
    <form class="box-form-1 mb-5">
    <h2>Bảo Lãnh Tội Phạm</h2>
    <hr>

    <!--  -->
    <div class="form-group-1">
        <label id="label-1">Tên Người Giải Quyết: </label>
        <input class="input-ia" type="text" id="input-1" value="" placeholder="Tên người đang giải quyết bảo lãnh"
            required>
    </div>
    <div class="form-group-1">
        <label id="label-2">Tên Người Bảo Lãnh: </label>
        <input class="input-ia" type="text" id="input-2" value="" placeholder="Tên công dân đến bảo lãnh " required>
    </div>
    <div class="form-group-1">
        <label id="label-3">Tên Người Vi Phạm: </label>
        <input class="input-ia" type="text" id="input-3" value="" placeholder="Tên đối tượng vi phạm" required>
    </div>
    <div class="form-group-1">
        <label id="label-4">CCCD: </label>
        <input class="input-ia" type="number" id="input-4" value="" placeholder="CCCD người vi phạm" required>
    </div>
    <div class="form-group-1">
        <label id="label-5">Tội Danh: </label>
        <!-- <input class="input-ia h6" type="text" id="input-5" value="" required> -->
        <textarea class="input-ia h6 area-input" type="text" id="input-5"
            placeholder="Tự nhập tay hoặc chọn phía bên dưới" required oninput="autoResize(this)"></textarea>
        <button class="btn btn-danger float-right" onclick="resetValue()" type="button">Reset</button>
    </div>

    <!-- Hộp Đựng Luật -->
    <div class="box-criminal-law mb-3">
        <p></p>
        <div class="row">
            <div class="col-md-6">
                <h6>Mức Độ 1:</h6>
                <div class="box-law">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tội danh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Cản trở người thi hành công vụ</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Hỗ trợ đồng bọn, trợ giúp tội phạm</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Không giao nộp hung khí gây án</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Tàng trữ hoặc sử dụng Giáp trái phép</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Giả dạng, tự xưng là người nhà nước</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Gây rối trật tự công cộng</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Trộm cắp tài sản công dân</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Phá hoại tài sản nhà nước</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Sử dụng nấm đấm gây rối trật tự công cộng</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 20, 'input-5', 'input-6', 'input-7')">Gây rối trước trụ sở cơ quan nhà nước</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Mức Độ 2:</h6>
                <div class="box-law">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tội danh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Nhập cư trái phép</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Tàng trữ chất cấm trái phép (Cây thảo dược)</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Có lời lẽ xúc phạm đến danh dự, nhân phẩm người khác</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Sử dụng vũ khí thô sơ nơi công cộng</td>
                            </tr>
                            <tr>
                                <td id="not-qs" onclick="selectSuggestion(this, 90, 'input-5', 'input-6', 'input-7')">Sử dụng vũ khí nóng nơi công cộng+Tàng trữ vũ khí nóng trái phép+Sử dụng vũ khí nóng trái phép</td>
                            </tr>
                            <tr>
                                <td id="yet-qs" onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Sử dụng vũ khí nóng nơi công cộng(Có Giấy NVQS)</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Tàng trữ vũ khí nóng</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Gây thương tích chưa nghiêm trọng cho người khác</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Xâm nhập trụ sở, nơi làm việc ban ngành nhà nước</td>
                            </tr>
                            <tr>
                                <td onclick="selectSuggestion(this, 30, 'input-5', 'input-6', 'input-7')">Sử dụng phương tiện gây thương tích chưa nghiêm trọng</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group-1">
        <label id="label-6">Mức Án: </label>
        <input class="input-ia" type="number" id="input-6" value="" oninput="tuDongTinhTien()"
            placeholder="Tự động hoặc tự tính tay" required>
    </div>
    <div class="form-group-1">
        <label id="label-7">Số Tiền: </label>
        <input class="input-ia" type="text" id="input-7" value="" placeholder="Tự động hoặc tự tính tay" required
            readonly>
    </div>
    <!--  -->
    <hr>
    <button class="copy-btn" onclick="copyAllToClipboard()">Sao Chép</button>
    <!-- <button class="reset-btn" onclick="resetValue()">Xóa dữ liệu</button> -->
</form>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/form_criminal_bail.js') }}"></script>
@endpush