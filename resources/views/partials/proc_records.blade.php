@extends('layouts.app')

@section('title', 'Hỗ Trợ Xử Án')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/proc_records.css') }}">
@endpush
@section('content')
    <div id="toast" class="toast-custom">Đã sao chép tội danh!</div>

    <div class="container container-responsive">
        <div class="group-function">
            <div class="function_law mb-4 p-2">
                <div class="func_01">
                    <ul class="list-unstyled d-flex flex-row justify-content-between align-items-center">
                        <li class="text-center checkbox_text">
                            <input type="number" class="input_proc form-control gioi_han_number" id="input-proc-1" min="1"
                                value="5">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input"
                                id="viPhamLuatTieuDungCheck">
                            <label for="input-proc-1">Vi phạm luật người tiêu dùng</label>
                        </li>
                        <li class="checkbox_text">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input" id="input-proc-4"
                                data-toidan="Sử dụng vũ khí nóng nơi công cộng (Có Giấy NVQS)" data-phut="30"
                                data-mucdo="2">
                            <label for="input-proc-4">Có giấy NVQS</label>
                        </li>
                        <li class="checkbox_text">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input" id="input-proc-5"
                                data-toidan="Tàng trữ vũ khí nóng trái phép" data-phut="30" data-mucdo="2">
                            <label for="input-proc-5">Chỉ có tội tàng trữ</label>
                        </li>
                        <li class="checkbox_text">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input" id="input-proc-6"
                                data-toidan="hợp tác" data-phut="-10" data-type="giam">
                            <label for="input-proc-6">Có hợp tác giảm 10p</label>
                        </li>
                        <li class="checkbox_text">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input" id="input-proc-7"
                                data-toidan="điều tra" data-phut="-10" data-type="giam">
                            <label for="input-proc-7">Giảm 10p điều tra</label>
                        </li>
                    </ul>
                </div>
                <div class="func_02 input_func d-flex flex-row gap-3">
                    <div class="input_func_01 d-flex align-items-center gap-2">
                        <input type="text" class="input_proc form-control" id="input-proc-2" placeholder="Nhập tên">
                        <label>Tên</label>
                    </div>
                    <div class="input_func_02 d-flex align-items-center gap-2">
                        <input type="number" class="input_proc form-control" id="input-proc-3" placeholder="Nhập CCCD">
                        <label>CCCD</label>
                    </div>
                </div>
            </div>
            <div class="copy_frame" id="copyFrame">
                <form action="/">
                    <ul class="list-unstyled p-3">
                        <li>Tên: <span class="text_show" id="show-name"></span></li>
                        <li>CCCD: <span class="text_show" id="show-cccd"></span></li>
                        <li>Tội Danh:
                            <span class="text_show" id="show-toiDanh"></span>
                        </li>
                        <li>Mức Án: <span class="text_show" id="show-soPhut"></span></li>
                        <li>Đã xử lý</li>
                    </ul>
                </form>
            </div>
            <div class="d-flex justify-content-around">
                <button class="btn-copy" id="copyToiDanhBtn"><strong>Chỉ Sao Chép Tội Danh</strong></button>
                <button class="btn-reset-toi-danh" id="btnResetToiDanh"><strong>Reset Tội Danh</strong></button>
            </div>
        </div>
        <div class="box-form-1">
            <div class="masonry-columns">
                <!-- Mức độ 1 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo1" aria-expanded="true" aria-controls="mucDo1">
                            Mức Độ 1
                            <i class="fa-solid fa-angle-down ms-2 transition"></i>
                        </button>
                    </h6>
                    <div id="mucDo1" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law-td" data-toidan="Cản trở người thi hành công vụ" data-phut="20">
                                        Cản trở người thi hành công vụ</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Hỗ trợ đồng bọn, trợ giúp tội phạm" data-phut="20">
                                        Hỗ trợ đồng bọn, trợ giúp tội phạm</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Không giao nộp hung khí gây án" data-phut="20">
                                        Không giao nộp hung khí gây án</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Tàng trữ hoặc sử dụng Giáp trái phép" data-phut="20">
                                        Tàng trữ hoặc sử dụng Giáp trái phép</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Giả dạng, tự xưng là người nhà nước" data-phut="20">
                                        Giả dạng, tự xưng là người nhà nước</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Gây rối trật tự công cộng" data-phut="20">Gây
                                        rối trật tự công cộng</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Trộm cắp tài sản công dân" data-phut="20">Trộm
                                        cắp tài sản công dân</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Phá hoại tài sản nhà nước" data-phut="20">Phá
                                        hoại tài sản nhà nước</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Sử dụng nấm đấm gây rối trật tự công cộng"
                                        data-phut="20">Sử dụng nấm đấm gây rối trật tự công cộng</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Gây rối trước trụ sở cơ quan nhà nước" data-phut="20">
                                        Gây rối trước trụ sở cơ quan nhà nước</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 3 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo3" aria-expanded="true" aria-controls="mucDo3">
                            Mức Độ 3
                            <i class="fa-solid fa-angle-down ms-2 transition"></i>
                        </button>
                    </h6>
                    <div id="mucDo3" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công người khác gây thương tích nghiêm trọng" data-mucdo="cong_don"
                                        data-phut="60">

                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công người khác gây thương tích nghiêm trọng
                                        </div>

                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Sử dụng vũ khí tấn công Quân đội" data-phut="60" data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công Quân đội
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Sử dụng vũ khí tấn công Giảng viên Học viện(PA)" data-phut="60"
                                        data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công Giảng viên Học viện(PA)
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Sử dụng vũ khí tấn công Nhân viên MW" data-phut="60"
                                        data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công Nhân viên MW
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Sử dụng vũ khí tấn công FIB" data-phut="60" data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công FIB
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Mua bán vũ khí trái phép" data-phut="60">
                                        Mua bán vũ khí trái phép
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 5 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mucDo5">
                            Mức Độ 5 <i class="fa-solid fa-angle-down"></i>
                        </button>
                    </h6>
                    <div id="mucDo5" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law-td"
                                        data-toidan="Lợi dụng quyền ra toà để bỏ trốn hoặc bỏ trốn trong thời gian được tại ngoại chờ ra toà"
                                        data-phut="180">
                                        Lợi dụng quyền ra toà để bỏ trốn hoặc bỏ trốn trong thời gian được tại ngoại
                                        chờ ra toà
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Tấn công Thống Đốc, Phó Thống Đốc, Nhân viên Chính Phủ"
                                        data-phut="180">
                                        Tấn công Thống Đốc, Phó Thống Đốc, Nhân viên Chính Phủ
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Đột nhập Trụ sở làm việc Học Viện Quốc Gia"
                                        data-phut="180">
                                        Đột nhập Trụ sở làm việc Học Viện Quốc Gia
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Đột nhập Trụ sở làm việc Nhà Tù " data-phut="180">
                                        Đột nhập Trụ sở làm việc Nhà Tù
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Đột nhập Trụ sở làm việc Quân Khu" data-phut="180">
                                        Đột nhập Trụ sở làm việc Quân Khu
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 2 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo2" aria-expanded="true" aria-controls="mucDo2">
                            Mức Độ 2
                            <i class="fa-solid fa-angle-down ms-2 transition"></i>
                        </button>
                    </h6>
                    <div id="mucDo2" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Nhập cư trái phép" data-phut="30">
                                        Nhập cư trái phép
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Tàng trữ chất cấm trái phép (Cây thảo dược)" data-phut="30">
                                        Tàng trữ chất cấm trái phép (Cây thảo dược)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Vu khống người khác" data-phut="30">
                                        Vu khống
                                        người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Có lời lẽ xúc phạm đến danh dự, nhân phẩm người khác" data-phut="30">Có
                                        lời lẽ xúc phạm đến danh dự, nhân phẩm người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Sử dụng vũ khí thô sơ nơi công cộng"
                                        data-phut="30">Sử dụng vũ
                                        khí thô sơ nơi công cộng
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" id="td-dac-biet-1"
                                        data-toidan="Sử dụng vũ khí nóng nơi công cộng+Tàng trữ vũ khí nóng trái phép+Sử dụng vũ khí nóng trái phép"
                                        data-phut="90">Sử dụng vũ khí nóng nơi công cộng
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Lừa đảo chiếm đoạt tài sản người khác"
                                        data-phut="30">Lừa đảo
                                        chiếm đoạt tài sản người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Chống đối người thi hành công vụ"
                                        data-phut="30">Chống đối người thi hành công vụ
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Xâm nhập trụ sở, nơi làm việc ban ngành nhà nước" data-phut="30">
                                        Xâm nhập trụ sở, nơi làm việc ban ngành nhà nước
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Xúc phạm Sĩ quan cảnh sát"
                                        data-phut="30">Xúc
                                        phạm Sĩ quan cảnh sát
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Gây thương tích chưa nghiêm trọng cho người khác" data-phut="30">
                                        Gây thương tích chưa nghiêm trọng cho người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Sử dụng phương tiện gây thương tích chưa nghiêm trọng" data-phut="30">
                                        Sử dụng phương tiện gây thương tích chưa nghiêm trọng
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 4 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo4">
                            Mức Độ 4 <i class="fa-solid fa-angle-down"></i>
                        </button>
                    </h6>
                    <div id="mucDo4" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công Nhân viên Y Tế (EMS)" data-mucdo="cong_don" data-phut="120">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công Nhân viên Y Tế (EMS)
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Tấn công Cán bộ cấp cao, Quản lý ban ngành thuộc Nhà nước"
                                        data-phut="120">
                                        Tấn công Cán bộ cấp cao, Quản lý ban ngành thuộc Nhà nước
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Có lời nói, hành động đe doạ Nhân viên Chính Phủ" data-phut="120">
                                        Có lời nói, hành động đe doạ Nhân viên Chính Phủ
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Tấn công nhân viên báo chí"
                                        data-phut="120">
                                        Tấn công nhân viên báo chí
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 6 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mucDo6">
                            Mức Độ 6 <i class="fa-solid fa-angle-down"></i>
                        </button>
                    </h6>
                    <div id="mucDo6" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center bdtp"
                                        data-toidan="Bạo Động Thành Phố" data-mucdo="cong_don" data-phut="200">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Bạo Động Thành Phố
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center tcts"
                                        data-toidan="Tấn công trụ sở, nơi làm việc thuộc Ban ngành Nhà nước"
                                        data-mucdo="cong_don" data-phut="200">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công trụ sở, nơi làm việc thuộc Ban ngành Nhà nước
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mức độ 7 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mucDo7">
                            Những tội danh vi phạm khác (Những tội danh này không phạt bill) <i
                                class="fa-solid fa-angle-down"></i>
                        </button>
                    </h6>
                    <div id="mucDo7" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law-td" data-toidan="Đào ngũ Quân Khu" data-phut="480">
                                        Đào ngũ Quân Khu
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Vi phạm quy tắc ngành" data-phut="360">
                                        Vi phạm quy tắc ngành
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/proc_records.js') }}"></script>
@endpush