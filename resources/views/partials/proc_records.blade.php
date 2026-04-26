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
                    <div class="p-2 fst-italic text-muted">
                        <small>Không lạm dụng phần hỗ trợ này, không rõ luật vui lòng vào đọc lại luật tại <a href="https://sites.google.com/view/info-gta5vn/b%E1%BB%99-lu%E1%BA%ADt-los-santos?authuser=0" target="_blank">Bộ luật S1</a></small>
                    </div>
                    <ul class="list-unstyled d-flex flex-row justify-content-between align-items-center">
                        <li class="text-center checkbox_text">
                            <input type="number" class="input_proc form-control gioi_han_number" id="input-proc-1" min="1"
                                value="6">
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
                        <li class="checkbox_text">
                            <input type="checkbox" class="custom_check_01 input_proc form-check-input" id="input-proc-9"
                                data-toidan="đầu thú" data-phut="-10" data-type="giam">
                            <label for="input-proc-9">Giảm 10p đầu thú</label>
                        </li>
                        <li class="checkbox_text">
                            <input type="number" class="input_proc form-control gioi_han_number" id="input-proc-8"
                                value="5">
                            {{--<input type="checkbox"
                                class="custom_check_01 input_proc form-check-input giamphutcustomer">--}}
                            <label for="input-proc-8">Giảm phút tự chọn</label>
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
                    {{--<ul class="list-unstyled p-3">
                        <li>Tên: <span class="text_show" id="show-name"></span></li>
                        <li>CCCD: <span class="text_show" id="show-cccd"></span></li>
                        <li>Tội Danh:
                            <span class="text_show" id="show-toiDanh"></span>
                        </li>
                        <li>Mức Án: <span class="text_show" id="show-soPhut"></span></li>
                        <li>Đã xử lý</li>
                    </ul> --}}
                    <p class="p-3">
                        Tên: <span class="text_show" id="show-name"></span> <br>
                        CCCD: <span class="text_show" id="show-cccd"></span> <br>
                        Tội Danh: <span class="text_show" id="show-toiDanh"></span> <br>
                        Mức Án: <span class="text_show" id="show-soPhut"></span> <br>
                        Đã xử lý
                    </p>
                </form>
            </div>
            <div class="d-flex justify-content-around">
                <button class="btn-copy" id="copyToiDanhBtn"><strong>Chỉ Sao Chép Tội Danh</strong></button>
                <button class="btn-reset-toi-danh" id="btnResetToiDanh"><strong>Reset Tội Danh</strong></button>
            </div>
        </div>
        <div class="box-form-1">
            <div class="masonry-columns">
                <!-- Khoản 1 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo1" aria-expanded="true" aria-controls="mucDo1">
                            Khoản 1 (20p)
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
                                    <td class="law-td" data-toidan="Không giao nộp tang vật trong vụ án" data-phut="20">
                                        Không giao nộp tang vật trong vụ án</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Tàng trữ hoặc sử dụng vật phẩm trái phép tại nơi công cộng" data-phut="20">
                                        Tàng trữ hoặc sử dụng vật phẩm trái phép tại nơi công cộng</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Gây rối trật tự công cộng" data-phut="20">Gây
                                        rối trật tự công cộng</td>
                                </tr>
                                <tr>
                                    <td class="law-td" data-toidan="Trộm cắp bất hợp pháp tài sản công dân (Nhập Nha Khiên Đồ)" data-phut="20">
                                        Trộm cắp bất hợp pháp tài sản công dân (Nhập Nha Khiên Đồ)</td>
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
                <!-- Khoản 3 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo3" aria-expanded="true" aria-controls="mucDo3">
                            Khoản 3 (60p) Không được bảo lãnh
                            <i class="fa-solid fa-angle-down ms-2 transition"></i>
                        </button>
                    </h6>
                    <div id="mucDo3" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh (Không được bảo lãnh)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công người khác gây thương tích nghiêm trọng" data-mucdo="cong_don"
                                        data-phut="60">

                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công người khác gây thương tích <span class="text-danger h5">nghiêm trọng</span>
                                        </div>

                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công sĩ quan, nhân viên Ban Ngành Nhà Nước đang thực hiện nhiệm vụ" data-phut="60" data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công sĩ quan, nhân viên Ban Ngành Nhà Nước đang thực hiện nhiệm vụ
                                        </div>
                                        <details>
                                            <summary>Xem luật</summary>
                                            <p>
                                                Lưu ý: 
                                                <br>
                                                * Trong cùng 1 thời điểm và cùng 1 khu vực hành vi tấn công vào nhiều người nhà nước vẫn sẽ tính là 1 lần tấn công. <br>
                                                * Số lần gây thương tích nghiêm trọng vẫn sẽ được cộng dồn để quy mức án. 
                                                <br>
                                                * Nếu sử dụng phương tiện vô tình va vào thì không cấu thành hành vi. Vô tình từ 2 lần trở lên thì sẽ cấu thành tội danh. 
                                                <br>
                                                * Việc vô tình hay cố ý sẽ tuỳ theo nhận định của Cảnh Sát và ban ngành chức năng nhìn nhận sự việc diễn ra. CP là cơ quan cuối cùng đưa ra quyết định nếu sảy ra kiện cáo.
                                                <br>
                                                * CRM hoặc Tấn Công Ban Ngành không lý do sẽ được xử lý Non-RP bởi Bộ luật Non-RP của máy chủ GTA5VN.
                                            </p>
                                        </details>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                        
                                    </td>
                                </tr>
                                {{-- 
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Sử dụng vũ khí tấn công Cảnh sát" data-phut="60" data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công Cảnh sát
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
                                        data-toidan="Sử dụng vũ khí tấn công Giảng viên Học viện (PA)" data-phut="60"
                                        data-mucdo="cong_don">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Sử dụng vũ khí tấn công Giảng viên Học viện (PA)
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
                                --}}
                                <tr>
                                    <td class="law-td" data-toidan="Rao bán, hỏi mua vũ khí nóng trái phép tại nơi công cộng" data-phut="60">
                                        Rao bán, hỏi mua vũ khí nóng trái phép tại nơi công cộng (các kênh chát ingame hoặc tương tác mic trực tiếp)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Khoản 5 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mucDo5">
                            Khoản 5 (180p) <i class="fa-solid fa-angle-down"></i>
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
                                    <td class="law-td text-danger fw-bold"
                                        data-toidan="Tấn Công Thống Đốc, Thị Trưởng Thành Phố."
                                        data-phut="180" data-mucdo="2">
                                        Tấn Công Thống Đốc, Thị Trưởng Thành Phố.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Khoản 6 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mucDo6">
                            Khoản 6 (200p)<i class="fa-solid fa-angle-down"></i>
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
                <!-- Khoản 2 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo2" aria-expanded="true" aria-controls="mucDo2">
                            Khoản 2 (30p)
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
                                        Nhập cư trái phép (Không đăng truy nã)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Tàng trữ chất cấm trái phép" data-phut="30">
                                        Tàng trữ chất cấm trái phép (Không đăng truy nã)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Vu khống người khác" data-phut="30">
                                        Vu khống người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Lăng mạ, xúc phạm đến người khác" data-phut="30">
                                        Lăng mạ, xúc phạm đến người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Hành vi, lời lẽ xúc phạm đến người ban ngành đang làm nhiệm vụ" data-phut="30">
                                       Hành vi, lời lẽ xúc phạm đến người ban ngành đang làm nhiệm vụ (Không đăng truy nã)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Giả dạng, tự xưng là người nhà nước" data-phut="30">
                                       Giả dạng, tự xưng là người nhà nước
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Sử dụng vũ khí thô sơ nơi công cộng"
                                        data-phut="30">
                                        Sử dụng vũ khí <span class="text-primary h5">thô sơ</span> nơi công cộng (Không đăng truy nã)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" id="td-dac-biet-1"
                                        data-toidan="Sử dụng vũ khí nóng nơi công cộng+Tàng trữ vũ khí nóng trái phép+Sử dụng vũ khí nóng trái phép"
                                        data-phut="90">Sử dụng vũ khí <span class="text-danger h5">Nóng</span><small> (súng) </small> nơi công cộng
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Lừa đảo chiếm đoạt tài sản người khác"
                                        data-phut="30">Lừa đảo chiếm đoạt tài sản người khác (dù lừa đảo được hay chưa được)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Chống đối người thi hành công vụ"
                                        data-phut="30">Chống đối người thi hành công vụ (Không đăng truy nã)
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Xâm nhập trụ sở, nơi làm việc thuộc Ban Ngành Nhà Nước" data-phut="30">
                                        Xâm nhập trụ sở, nơi làm việc thuộc Ban Ngành Nhà Nước
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Hành động, lời nói, kênh chat ingame đe doạ người thuộc Ban Ngành Nhà Nước đang làm nhiệm vụ"
                                        data-phut="30">Hành động, lời nói, kênh chat ingame đe doạ người thuộc Ban Ngành Nhà Nước đang làm nhiệm vụ
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Gây thương tích chưa nghiêm trọng cho người khác" data-phut="30">
                                        Gây thương tích <span class="text-danger h5">chưa nghiêm trọng</span> cho người khác
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Sử dụng phương tiện gây thương tích chưa nghiêm trọng" data-phut="30">
                                        Sử dụng <span class="text-danger h5">phương tiện</span> gây thương tích chưa nghiêm trọng
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Khoản 4 -->
                <div class="masonry-item">
                    <h6>
                        <button class="muc_do btn text-decoration-none d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mucDo4">
                            Khoản 4 (120p) Không được bảo lãnh <i class="fa-solid fa-angle-down"></i>
                        </button>
                    </h6>
                    <div id="mucDo4" class="collapse show box-law">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tội danh Không được bảo lãnh. </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công Quản Lý Ban Ngành (QLBN)" data-mucdo="cong_don" data-phut="120">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công Quản Lý Ban Ngành (QLBN)
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công Nhân Viên Văn Phòng Chính Phủ (VPCP)" data-mucdo="cong_don" data-phut="120">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công Nhân Viên Văn Phòng Chính Phủ (VPCP)
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="law_input_content law-td d-flex justify-content-between align-items-center"
                                        data-toidan="Tấn công Nhân Viên Truyền Thông đang làm nhiệm vụ" data-mucdo="cong_don" data-phut="120">
                                        <div class="td-label flex-grow-1 pe-2">
                                            Tấn công Nhân Viên Truyền Thông đang làm nhiệm vụ
                                        </div>
                                        <div class="td-input">
                                            <input type="number" value="1" min="1"
                                                class="form-control gioi_han_number law-count-input" />
                                        </div>
                                    </td>
                                </tr>
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
                                        data-toidan="Xâm Nhập Trái Phép Khu Quân Sự"
                                        data-phut="120">
                                        Xâm Nhập Trái Phép Khu <span class="text-danger h5">Quân Sự</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td"
                                        data-toidan="Xâm Nhập Trái Phép Nhà Tù" data-phut="120">
                                        Xâm Nhập Trái Phép <span class="text-dark h5">Nhà Tù</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-mucdo="2" class="law-td" data-toidan="Xâm Nhập Trái Phép Học Viện Cảnh Sát"
                                        data-phut="120">
                                        Xâm Nhập Trái Phép <span class="text-primary h5">Học Viện Cảnh Sát</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Khoản 7 -->
                {{-- <div class="masonry-item">
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
                </div>--}}
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('assets/js/proc_records.js') }}"></script>
@endpush