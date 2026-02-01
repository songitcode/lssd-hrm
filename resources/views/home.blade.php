@extends('layouts.app')

@section('title', 'Trang Chủ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hr_employees.css') }}">
@endpush

@section('content')
    {{-- TOP Onduty --}}
    <div class="container">
        @include('partials.top_onduty')
    </div>

    <div class="container">
        <details>
            <summary class="od-control-btn m-4 text-center">XEM - KHEN THƯỞNG THÀNH TÍCH 2025</summary>
            <div class="row gap-2 my-5 text-center">
                <div class="od-top-1-container col-md-6">
                    <h6 class="lssd-control-btn"><small>Vinh danh các Sĩ Quan có thành tích xuất sắc sự kiện “BE THE WATCH –
                            BRING
                            THE PEACE - 2025”</small></h6>
                    <div class="row mt-4">
                        @php
                            $soCongImage = [5, 6, 7, 8, 9];
                            $nameKhenThuong = [
                                5 => 'HERO OF JUSTICE - Mr Hungzz',
                                6 => 'TOP 2 - Tien Brian',
                                7 => 'TOP 3 - Luan Topp',
                                8 => 'NEW STAR OF JUSTICE - Phuc Qqq',
                                9 => 'TOP 2 - Mr Tigerrr',
                            ];
                        @endphp
                        @foreach ($soCongImage as $sci)
                            <div class="card-event-home mb-5 col-md-3">
                                <div class="image-container">
                                    <img src="{{ asset('assets/images/khen_thuong_folder/' . $sci . '.jpg') }}" alt=""
                                        class="poster-card-home">
                                    <div class="event-date">
                                        <div class="date-month">{{ $nameKhenThuong[$sci] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="od-top-1-container col-md-6">
                    <h5 class="lssd-control-btn"><small>Vinh Danh Đại Hội Võ Lâm 2025</small></h5>
                    <div class="d-flex gap-3 m-4 justify-content-center">
                        <div class="card-event-home">
                            <div class="image-container">
                                <img src="{{ asset('assets/images/dai_hoi_vo_lam_1.png') }}" alt=""
                                    class="poster-card-home">
                                <div class="event-date">
                                    <div class="date-month">Đại hội võ lâm đạt Top 4</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </details>

        <div class="row group_home_01 mt-5 align-items-stretch">
            <div class="col-lg-8 mb-3 ">
                <div class="box_display_home table-responsive card_table_employees od-top-1-container">
                    <h4 class="lssd-control-btn"><small>Thành Viên LLCS LSSD</small></h4>
                    <table class="table table-sm text-center table_employees table-hover mt-3">
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
                                <tr
                                    class="{{ in_array($user->position->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'glow' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($user->employee?->discord_id)
                                            <img id="avatarPreview" src="{{ $user->employee->discord_avatar }}" alt="Avatar"
                                                class="rounded-circle" width="40" height="40"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=random';">
                                        @else
                                            @if ($user->employee?->avatar)
                                                <img id="avatarPreview" src="{{ asset('storage/' . $user->employee->avatar) }}"
                                                    alt="Avatar" class="rounded-circle" width="40" height="40"
                                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=random';">
                                            @else
                                                <img id="avatarPreview"
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->employee->name_ingame ?? 'Admin') }}&background=random"
                                                    class="rounded-circle" width="40" height="40" alt="Default">
                                            @endif
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
                    <div class="row d-flex text-center align-items-center">
                        <div class="col-md-6 mb-2">
                            <div class="info-box ">
                                <strong>
                                    <i class="fa-solid fa-users"></i> TV LSSD:
                                    {{ $tongSoNhanVien }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="info-box">
                                <strong>
                                    <i class="fa-solid fa-crown"></i> TV cấp cao:
                                    {{ $soNhanVienCapCao }}
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="info-box">
                        <strong>
                            <p>Chào mừng bạn đến với trang chủ của LLCS LSSD!</p>
                            <p>Xem các thông tin và cập nhật mới nhất trên Discord LSSD.</p>
                            <p>Chúc bạn một ngày làm việc đầy vui vẻ.</p>
                        </strong>
                    </div>
                </div>
                <div class="box_display_home card_right_bottom mt-3 p-2 od-top-1-container">
                    <div class="banner_lssd_home">
                        <img src="{{ asset('assets/images/banner_lssd.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container lssd-container mt-5">
        <h4 class="lssd-control-btn width-fit-ctn ms-5">Tính Năng</h4>
        <div class="row">
            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5" href="{{ route('attendance.index') }}" target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Chấm Công</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5" href="{{ route('partials.criminal_bail') }}"
                    target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Bảo Lãnh Tội Phạm</b></p>
                </a>
            </div>

            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5"
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
                <a class="card lssd container lssd-card mt-5 mb-5 {{ request()->is('don-xin-nghi-phep') ? 'active-link' : '' }}"
                    href="{{ route('partials.take_leave') }}" target="_blank">
                    <div class="overlay"></div>
                    <div class="circle">
                        <img class="mb-2" src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="" width="100" height="100">
                    </div>
                    <p><b>Đơn Xin Nghỉ Phép</b></p>
                </a>
            </div>

            <!-- Văn hóa đồn -->
            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5 card-img">
                    <img src="{{ asset('assets/images/VAN_HOA_LSSD.png') }}" class="img-thumbnail" data-bs-toggle="modal"
                        data-bs-target="#imageModal" onclick="changeImage(this)" width="100" height="100"
                        style="object-fit: cover;">
                    <p class="text-dark"><b>Văn Hóa Đồn</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5 card-img">
                    <img src="{{ asset('assets/images/dieu_cam__nguyen_tac_nganh.png') }}" class="img-thumbnail"
                        data-bs-toggle="modal" data-bs-target="#imageModal" onclick="changeImage(this)" width="100"
                        height="100" style="object-fit: cover;">
                    <p class="text-dark"><b>Nguyên Tắc Ngành</b></p>
                </a>
            </div>
            <div class="col-lg-3">
                <a class="card lssd container lssd-card mt-5 mb-5 card-img">
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

<script src="https://cdn.jsdelivr.net/gh/Sean-93/newmarquee@v0.9.1/dist/newmarquee-min.js"></script>
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
        // Fireworks effect
        // Firework chỉ bắn khi load hoặc click
        function createFirework() {
            const firework = document.createElement('div');
            firework.classList.add('firework');

            const colors = ['#ffdd00', '#00eaff', '#ffffff', '#ff8a00'];
            firework.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            firework.style.left = Math.random() * 100 + 'vw';
            firework.style.top = Math.random() * 100 + 'vh';

            firework.style.setProperty('--x', (Math.random() - 0.5) * 300 + 'px');
            firework.style.setProperty('--y', (Math.random() - 0.5) * 300 + 'px');

            document.body.appendChild(firework);
            setTimeout(() => firework.remove(), 1000);
        }

        // Firework lúc load trang
        for (let i = 0; i < 15; i++) {
            setTimeout(createFirework, i * 150);
        }

        // Firework khi click vào màn hình
        document.addEventListener("click", createFirework);

        // Create occasional fireworks
        setInterval(createFirework, 300);

        // Create initial fireworks
        for (let i = 0; i < 20; i++) {
            setTimeout(createFirework, i * 100);
        }
    </script>
@endpush