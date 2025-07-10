@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="row group_home_01 mt-3 align-items-stretch">
            <div class="col-lg-8 mb-3">
                <div class="box_display_home table-responsive card_table_employees">
                    <h4 class="border-bottom-h4-home"><strong>Thành Viên LLCS LSSD</strong></h4>
                    <table class="table table-sm text-center table_employees table-hover">
                        <thead>
                            <tr class="head-table-employees-home">
                                <th>STT</th>
                                <th>AVT</th>
                                <th>Tên Sĩ Quan</th>
                                <th>Chức Vụ</th>
                                <th>Quân Hàm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($user->employee?->avatar)
                                            <img src="{{ asset('storage/' . $user->employee->avatar) }}" alt="AVT" width="40"
                                                height="40" class="rounded-circle">
                                        @else
                                            <img src="{{ asset('assets/images/user_preview_logo.png') }}" alt="Default"
                                                class="rounded-circle" width="40" height="40">
                                        @endif
                                    </td>
                                    <td>{{ $user->employee?->name_ingame ?? 'N/A' }}</td>
                                    <td>{{ $user->employee?->position?->name_positions ?? 'N/A' }}</td>
                                    <td>{{ $user->employee?->rank?->name_ranks ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3 phan_trang">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="box_display_home card_right_top">
                    <div class="glass-effect p-2">
                        <h5 class="title_welcome_home">Chào Mừng, sĩ quan<strong>
                                {{ auth()->user()->employee->name_ingame ?? auth()->user()->username }}</strong></h5>
                        <div class="popup">
                            <div class="popup-content">
                                <p>Chào mừng bạn đến với trang chủ của LLCS LSSD!</p>
                                <p>Xem các thông tin và cập nhật mới nhất trên Discord LSSD.</p>
                                <p>Chúc bạn một ngày làm việc đầy vui vẻ.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box_display_home card_right_bottom mt-3 p-2">
                    <div class="banner_lssd_home">
                        <img src="{{ asset('assets/images/banner_lssd.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="label-top ms-5 mt-5">
            <h4>Form Nhập Liệu</h4>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5" href="{{ route('attendance.index') }}" target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Chấm Công</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5" href="{{ route('partials.criminal_bail') }}" target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Bảo Lãnh Tội Phạm</b></p>
                </a>
            </div>

            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5"
                    href="https://sites.google.com/view/info-gta5vn/b%E1%BB%99-lu%E1%BA%ADt-los-santos?authuser=0"
                    target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Bộ Luật Liên Bang</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5" href="./pages/form_on_leave.html" target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Đơn Xin Nghỉ Phép</b></p>
                </a>
            </div>

            <div class="label-center ms-5 mt-5">
                <h4>Khác</h4>
            </div>

            <!-- Văn hóa đồn -->
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5 card-img">
                    <img src="{{ asset('assets/images/VAN_HOA_LSSD.png') }}" class="img-thumbnail" data-bs-toggle="modal"
                        data-bs-target="#imageModal" onclick="changeImage(this)" width="100" height="100"
                        style="object-fit: cover;">
                    <p class="text-dark"><b>Văn Hóa Đồn</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5 card-img">
                    <img src="{{ asset('assets/images/dieu_cam__nguyen_tac_nganh.png') }}" class="img-thumbnail"
                        data-bs-toggle="modal" data-bs-target="#imageModal" onclick="changeImage(this)" width="100"
                        height="100" style="object-fit: cover;">
                    <p class="text-dark"><b>Nguyên Tắc Ngành</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container mt-5 mb-5 card-img">
                    <img src="{{ asset('assets/images/luat_sung_dien.png') }}" class="img-thumbnail" data-bs-toggle="modal"
                        data-bs-target="#imageModal" onclick="changeImage(this)"
                        style="height: 90%; border: none; object-fit: cover;">
                    <p class="text-dark"><b>Quy Tắc Súng Điện</b></p>
                </a>
            </div>

            <!-- modal hiển thị ảnh -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img id="modalImg" src="" class="img-fluid" width="70%" height="70%" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
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
        function changeImage(img) {
            document.getElementById("modalImg").src = img.src;
        }
    </script>
@endpush