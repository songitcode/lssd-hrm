@extends('layouts.app')

@section('title', 'Tết Trung Thu 2026 • LLCS LSSD')
@section('hide_overlay')@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Cinzel:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/home_trungthu2026.css') }}">
@endpush

@php
$maintenance = false;
$quockhanh2026 = false;
@endphp

@section('content')
@if($maintenance && !View::hasSection('hide_maintenance'))
<div class="container d-flex align-items-center justify-content-center py-5">
    <div class="tt26-maintenance">
        <span>HỆ THỐNG TẠM ĐÓNG</span>
        <strong>ĐANG CHUẨN BỊ ĐÊM TRĂNG...</strong>
    </div>
</div>
@else

<div class="tt26-page container">

    {{-- =========================================================
         ATMOSPHERE / BACKGROUND
         ========================================================= --}}
    <div class="tt26-atmosphere" aria-hidden="true">
        <div class="tt26-stars tt26-stars-a"></div>
        <div class="tt26-stars tt26-stars-b"></div>
        <div class="tt26-shooting-star tt26-shooting-star-a"></div>
        <div class="tt26-shooting-star tt26-shooting-star-b"></div>

        <div class="tt26-cloud tt26-cloud-a"></div>
        <div class="tt26-cloud tt26-cloud-b"></div>
        <div class="tt26-cloud tt26-cloud-c"></div>

        <div class="tt26-lantern tt26-lantern-1" data-lantern>
            <span class="tt26-lantern-cap"></span>
            <span class="tt26-lantern-body"><i></i></span>
            <span class="tt26-lantern-tassel"></span>
        </div>
        <div class="tt26-lantern tt26-lantern-2" data-lantern>
            <span class="tt26-lantern-cap"></span>
            <span class="tt26-lantern-body"><i></i></span>
            <span class="tt26-lantern-tassel"></span>
        </div>
        <div class="tt26-lantern tt26-lantern-3" data-lantern>
            <span class="tt26-lantern-cap"></span>
            <span class="tt26-lantern-body"><i></i></span>
            <span class="tt26-lantern-tassel"></span>
        </div>
        <div class="tt26-lantern tt26-lantern-4" data-lantern>
            <span class="tt26-lantern-cap"></span>
            <span class="tt26-lantern-body"><i></i></span>
            <span class="tt26-lantern-tassel"></span>
        </div>
        <div class="tt26-lantern tt26-lantern-5" data-lantern>
            <span class="tt26-lantern-cap"></span>
            <span class="tt26-lantern-body"><i></i></span>
            <span class="tt26-lantern-tassel"></span>
        </div>
    </div>

    {{-- =========================================================
         FESTIVAL HERO
         ========================================================= --}}
    <section class="tt26-hero" aria-labelledby="tt26-hero-title">
        <div class="tt26-hero-noise"></div>
        <div class="tt26-hero-grid"></div>

        <div class="tt26-hero-ribbon">
            <span class="tt26-ribbon-dot"></span>
            TẾT TRUNG THU VIỆT NAM
            <span class="tt26-ribbon-line"></span>
            2026
        </div>

        <div class="tt26-hero-content">
            <div class="tt26-hero-copy">
                <span class="tt26-eyebrow">RẰM THÁNG TÁM · 25.09.2026 · LLCS LSSD</span>

                <h1 id="tt26-hero-title" class="tt26-hero-title">
                    <span>TRĂNG THU</span>
                    <em>NGHĨA TÌNH</em>
                    <small>GẮN KẾT NHỮNG TẤM LÒNG</small>
                </h1>

                <p class="tt26-hero-description">
                    Chào đón Trung Thu bằng ánh trăng,
                    đèn lồng, tiếng trống lân và tinh thần sum họp — đưa một
                    nét đẹp truyền thống Việt Nam vào không gian LLCS LSSD.
                </p>

                <div class="tt26-hero-actions">
                    <a href="#tt26-story" class="tt26-btn tt26-btn-primary">
                        <i class="fa-solid fa-moon"></i>
                        Khám phá đêm trăng
                    </a>
                    <a href="#lssd-officers" class="tt26-btn tt26-btn-ghost">
                        <i class="fa-solid fa-users"></i>
                        Xem nhân sự LSSD
                    </a>
                </div>

                <div class="tt26-live-note">
                    <span class="tt26-live-dot"></span>
                    <span>FESTIVAL MODE ACTIVE</span>
                    <b>Thắp vào một chiếc đèn lồng ✦</b>
                </div>
            </div>

            <div class="tt26-moon-scene" aria-label="Trăng rằm và thỏ ngọc">
                <div class="tt26-moon-halo"></div>
                <div class="tt26-moon">
                    <div class="tt26-moon-crater tt26-moon-crater-a"></div>
                    <div class="tt26-moon-crater tt26-moon-crater-b"></div>
                    <div class="tt26-moon-crater tt26-moon-crater-c"></div>
                    <div class="tt26-moon-crater tt26-moon-crater-d"></div>
                    <span>RẰM</span>
                </div>

                <div class="tt26-rabbit">
                    <span class="tt26-rabbit-ear tt26-rabbit-ear-a"></span>
                    <span class="tt26-rabbit-ear tt26-rabbit-ear-b"></span>
                    <span class="tt26-rabbit-head"></span>
                    <span class="tt26-rabbit-body"></span>
                    <span class="tt26-rabbit-tail"></span>
                </div>

                <div class="tt26-moon-caption">
                    <strong>ĐÊM TRĂNG 2026</strong>
                    <span>Thắp đèn · Rước đèn · Đoàn viên</span>
                </div>
            </div>
        </div>

        {{-- Countdown --}}
        <div class="tt26-countdown" id="tt26Countdown" aria-label="Đếm ngược đến Tết Trung Thu 2026">
            <div class="tt26-countdown-label">
                <span>ĐẾM NGƯỢC ĐÊM TRĂNG</span>
                <small>Rằm tháng Tám · 25/09/2026</small>
            </div>
            <div class="tt26-countdown-grid">
                <div class="tt26-time-box"><strong id="tt26Days">--</strong><span>NGÀY</span></div>
                <div class="tt26-time-box"><strong id="tt26Hours">--</strong><span>GIỜ</span></div>
                <div class="tt26-time-box"><strong id="tt26Minutes">--</strong><span>PHÚT</span></div>
                <div class="tt26-time-box"><strong id="tt26Seconds">--</strong><span>GIÂY</span></div>
            </div>
        </div>

        <div class="tt26-hero-bottom-line">
            <span>☾</span>
            Từ ký ức tuổi thơ đến không gian số hôm nay
            <span>✦</span>
        </div>
    </section>

    @if($quockhanh2026 && !View::hasSection('hide_quockhanh2026'))
    @include('quockhanh2026')
    @endif

    {{-- =========================================================
         CULTURAL STORY
         ========================================================= --}}
    <section id="tt26-story" class="tt26-section tt26-story-section">
        <div class="tt26-section-head" data-reveal>
            <span class="tt26-eyebrow">01 · CÂU CHUYỆN TRUNG THU</span>
            <h2>Đêm trăng <span>rằm</span></h2>
            <p>
                Trung Thu trong trải nghiệm này được chia thành bốn chất liệu
                quen thuộc: ánh trăng, đèn lồng, đoàn lân và khoảnh khắc quây quần.
                Mỗi chất liệu trở thành một lớp chuyển động để trang chủ có cảm giác
                như một “sân khấu đêm trăng” thay vì một dashboard thông thường.
            </p>
        </div>

        <div class="tt26-story-grid">
            <article class="tt26-story-card tt26-story-card-luna" data-reveal>
                <div class="tt26-card-symbol"><i class="fa-solid fa-moon"></i></div>
                <div>
                    <span class="tt26-card-kicker">ÁNH TRĂNG</span>
                    <h3>Rằm tháng Tám</h3>
                    <p>Trung tâm thị giác là mặt trăng lớn, tạo cảm giác yên bình và dẫn mắt xuống phần nội dung.</p>
                </div>
                <span class="tt26-card-number">01</span>
            </article>

            <article class="tt26-story-card tt26-story-card-lamp" data-reveal>
                <div class="tt26-card-symbol"><i class="fa-regular fa-lightbulb"></i></div>
                <div>
                    <span class="tt26-card-kicker">ĐÈN LỒNG</span>
                    <h3>Rước đèn</h3>
                    <p>Đèn lồng chuyển động nhẹ và có thể “thắp sáng” bằng một cú click — tạo tương tác thay vì chỉ trang trí.</p>
                </div>
                <span class="tt26-card-number">02</span>
            </article>

            <article class="tt26-story-card tt26-story-card-lion" data-reveal>
                <div class="tt26-card-symbol"><i class="fa-solid fa-drum"></i></div>
                <div>
                    <span class="tt26-card-kicker">MÚA LÂN</span>
                    <h3>Nhịp hội</h3>
                    <p>Màu đỏ, vàng và chuyển động nhịp nhàng gợi không khí lễ hội, nhưng vẫn giữ cấu trúc hiện đại của trang quản trị.</p>
                </div>
                <span class="tt26-card-number">03</span>
            </article>

            <article class="tt26-story-card tt26-story-card-family" data-reveal>
                <div class="tt26-card-symbol"><i class="fa-solid fa-people-roof"></i></div>
                <div>
                    <span class="tt26-card-kicker">ĐOÀN VIÊN</span>
                    <h3>Ở bên nhau</h3>
                    <p>Điểm cuối của câu chuyện là con người — nhân sự, cộng đồng và những lời chúc được đặt lại vào trung tâm.</p>
                </div>
                <span class="tt26-card-number">04</span>
            </article>
        </div>
    </section>

    {{-- =========================================================
         INTERACTIVE FESTIVAL MOMENT
         ========================================================= --}}
    <section class="tt26-festival-panel" data-reveal>
        <div class="tt26-festival-orbit"></div>
        <div class="tt26-festival-head">
            <div>
                <span class="tt26-eyebrow">02 · TƯƠNG TÁC</span>
                <h2>Thắp đèn đón trăng</h2>
            </div>
            <button class="tt26-mini-btn" type="button" id="tt26LightAll">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Thắp tất cả
            </button>
        </div>

        <div class="tt26-lantern-stage">
            <div class="tt26-stage-moon">
                <div class="tt26-stage-moon-core"></div>
                <span>☾</span>
            </div>

            <div class="tt26-stage-village">
                <span class="tt26-roof roof-1"></span>
                <span class="tt26-roof roof-2"></span>
                <span class="tt26-roof roof-3"></span>
                <span class="tt26-house house-1"></span>
                <span class="tt26-house house-2"></span>
                <span class="tt26-house house-3"></span>
            </div>

            <div class="tt26-stage-lantern-row">
                <button type="button" class="tt26-hanging-lantern tt26-hanging-red" data-stage-lantern>
                    <span class="tt26-hanging-body"><i>✦</i></span>
                    <small>ƯỚC</small>
                </button>
                <button type="button" class="tt26-hanging-lantern tt26-hanging-gold" data-stage-lantern>
                    <span class="tt26-hanging-body"><i>☾</i></span>
                    <small>BÌNH</small>
                </button>
                <button type="button" class="tt26-hanging-lantern tt26-hanging-orange" data-stage-lantern>
                    <span class="tt26-hanging-body"><i>✶</i></span>
                    <small>AN</small>
                </button>
                <button type="button" class="tt26-hanging-lantern tt26-hanging-red" data-stage-lantern>
                    <span class="tt26-hanging-body"><i>♡</i></span>
                    <small>VUI</small>
                </button>
                <button type="button" class="tt26-hanging-lantern tt26-hanging-gold" data-stage-lantern>
                    <span class="tt26-hanging-body"><i>✦</i></span>
                    <small>SUM VẦY</small>
                </button>
            </div>

            <div class="tt26-stage-ground"></div>
        </div>

        <div class="tt26-festival-message">
            <span class="tt26-message-icon">✦</span>
            <p>
                <strong>Mỗi ánh đèn là một lời chúc.</strong>
                Chúc bình an, ấm áp, thuận lợi và thật nhiều khoảnh khắc đáng nhớ bên những người mình quý.
            </p>
            <span class="tt26-message-icon">☾</span>
        </div>
    </section>

    {{-- =========================================================
         QUICK ACCESS
         ========================================================= --}}
    <section class="tt26-quick-section" aria-label="Truy cập nhanh">
        <div class="tt26-section-head tt26-section-head-row" data-reveal>
            <div>
                <span class="tt26-eyebrow">03 · HỆ THỐNG</span>
                <h2>Điểm truy cập trong đêm trăng</h2>
            </div>
            <span class="tt26-live-chip"><i></i> HỆ THỐNG SẴN SÀNG</span>
        </div>

        <div class="tt26-quick-grid">
            <a href="{{ route('attendance.index') }}" target="_blank" class="tt26-quick-card" data-reveal>
                <span class="tt26-quick-icon"><i class="fa-solid fa-clock"></i></span>
                <span class="tt26-quick-copy">
                    <strong>Chấm công</strong>
                    <small>Quản lý ca trực & thời gian</small>
                </span>
                <span class="tt26-quick-arrow">↗</span>
            </a>

            <a href="{{ route('partials.criminal_bail') }}" target="_blank" class="tt26-quick-card" data-reveal>
                <span class="tt26-quick-icon"><i class="fa-solid fa-file-circle-check"></i></span>
                <span class="tt26-quick-copy">
                    <strong>Bảo lãnh tội phạm</strong>
                    <small>Tra cứu & xử lý hồ sơ</small>
                </span>
                <span class="tt26-quick-arrow">↗</span>
            </a>

            <a href="https://sites.google.com/view/info-gta5vn/b%E1%BB%99-lu%E1%BA%ADt-los-santos?authuser=0" target="_blank" rel="noopener noreferrer" class="tt26-quick-card" data-reveal>
                <span class="tt26-quick-icon"><i class="fa-solid fa-book-open"></i></span>
                <span class="tt26-quick-copy">
                    <strong>Bộ luật Liên bang</strong>
                    <small>Quy chuẩn & điều lệ</small>
                </span>
                <span class="tt26-quick-arrow">↗</span>
            </a>

            <a href="{{ route('partials.proc_records') }}" target="_blank" class="tt26-quick-card" data-reveal>
                <span class="tt26-quick-icon"><i class="fa-solid fa-calendar-xmark"></i></span>
                <span class="tt26-quick-copy">
                    <strong>Hỗ trợ xử án</strong>
                    <small>Gửi yêu cầu hành chính</small>
                </span>
                <span class="tt26-quick-arrow">↗</span>
            </a>
        </div>
    </section>

    {{-- =========================================================
         STATS
         ========================================================= --}}
    <section class="tt26-stats" data-reveal>
        <div class="tt26-stat">
            <span class="tt26-stat-icon"><i class="fa-solid fa-users"></i></span>
            <span class="tt26-stat-copy">
                <strong>{{ $tongSoNhanVien }}</strong>
                <small>THÀNH VIÊN LSSD</small>
            </span>
        </div>

        <div class="tt26-stat-divider"></div>

        <div class="tt26-stat">
            <span class="tt26-stat-icon"><i class="fa-solid fa-crown"></i></span>
            <span class="tt26-stat-copy">
                <strong>{{ $soNhanVienCapCao }}</strong>
                <small>THÀNH VIÊN CẤP CAO</small>
            </span>
        </div>

        <div class="tt26-stat-divider"></div>

        <div class="tt26-stat">
            <span class="tt26-stat-icon"><i class="fa-solid fa-shield-halved"></i></span>
            <span class="tt26-stat-copy">
                <strong>LLCS</strong>
                <small>LỰC LƯỢNG CHẤP SỰ</small>
            </span>
        </div>
    </section>

    {{-- =========================================================
         PERSONNEL TABLE
         ========================================================= --}}
    <section class="tt26-personnel" id="lssd-officers">
        <div class="tt26-personnel-head" data-reveal>
            <div>
                <span class="tt26-eyebrow">04 · NHÂN SỰ LSSD</span>
                <h2>Những người giữ đêm trăng</h2>
                <p>Danh sách nhân sự LLCS LSSD SV1 - GTA5VN.</p>
            </div>
            <span class="tt26-live-chip"><i></i> ACTIVE</span>
        </div>

        <div class="tt26-table-wrap" data-reveal>
            <div class="tt26-table-glow"></div>
            <div class="table-responsive">
                <table class="tt26-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>AVT</th>
                            <th>TÊN SĨ QUAN</th>
                            <th>CHỨC VỤ</th>
                            <th>QUÂN HÀM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr class="{{ in_array($user->position?->name_positions, ['Cục Trưởng', 'Phó Cục Trưởng', 'Trợ Lý Cục Trưởng']) ? 'tt26-row-command' : '' }}">
                            <td class="tt26-td-index">{{ $index + 1 }}</td>

                            <td>
                                <img
                                    src="{{ $user->employee?->display_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->employee?->name_ingame ?? 'Admin') . '&background=2b1713&color=FFD86B&bold=true' }}"
                                    alt="Avatar"
                                    class="tt26-avatar"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->employee?->name_ingame ?? 'Admin') }}&background=2b1713&color=FFD86B&bold=true';">
                            </td>

                            <td class="tt26-td-name">
                                {{ $user->employee?->name_ingame ?? 'N/A' }}
                            </td>

                            <td>
                                <span class="tt26-position">
                                    {{ $user->employee?->position?->name_positions ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="tt26-rank">
                                {{ $user->employee?->rank?->name_ranks ?? 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tt26-pagination">
                {{ $users->links() }}
            </div>
        </div>
    </section>

    {{-- =========================================================
         SYSTEM FEATURES
         ========================================================= --}}
    <section class="tt26-features" id="lssd-features">
        <div class="tt26-section-head" data-reveal>
            <span class="tt26-eyebrow">05 · CÔNG CỤ HÀNH CHÍNH</span>
            <h2>Giữ trọn chất LSSD, thay áo đón trăng.</h2>
            <p>
                Chức năng không đổi — chỉ thay đổi câu chuyện thị giác để toàn bộ
                trang chủ hòa vào không khí Trung Thu 2026.
            </p>
        </div>

        <div class="tt26-feature-grid">
            <a class="tt26-feature-card" href="{{ route('attendance.index') }}" target="_blank" data-reveal>
                <span class="tt26-feature-icon"><i class="fa-solid fa-clock"></i></span>
                <span class="tt26-feature-copy">
                    <small>01</small>
                    <strong>Chấm Công</strong>
                    <em>Quản lý ca trực & thời gian</em>
                </span>
                <span class="tt26-feature-arrow">→</span>
            </a>

            <a class="tt26-feature-card" href="{{ route('partials.proc_records') }}" target="_blank" data-reveal>
                <span class="tt26-feature-icon"><i class="fa-solid fa-file-circle-check"></i></span>
                <span class="tt26-feature-copy">
                    <small>02</small>
                    <strong>Hỗ trợ xử án</strong>
                    <em>Tra cứu & xử lý hồ sơ</em>
                </span>
                <span class="tt26-feature-arrow">→</span>
            </a>

            <a class="tt26-feature-card" href="https://sites.google.com/view/info-gta5vn/b%E1%BB%99-lu%E1%BA%ADt-los-santos?authuser=0" target="_blank" rel="noopener noreferrer" data-reveal>
                <span class="tt26-feature-icon"><i class="fa-solid fa-book-open"></i></span>
                <span class="tt26-feature-copy">
                    <small>03</small>
                    <strong>Bộ Luật Liên Bang</strong>
                    <em>Quy chuẩn & điều lệ</em>
                </span>
                <span class="tt26-feature-arrow">→</span>
            </a>
        </div>
    </section>

    {{-- =========================================================
         FESTIVAL CLOSING
         ========================================================= --}}
    <section class="tt26-closing" data-reveal>
        <div class="tt26-closing-glow"></div>
        <div class="tt26-closing-content">
            <span class="tt26-eyebrow">06 · LỜI CHÚC</span>
            <div class="tt26-closing-moon">☾</div>
            <h2>Trăng sáng một nhà,<br><span>ấm áp muôn người.</span></h2>
            <p>
                Chúc toàn thể anh em LSSD và cộng đồng một mùa Trung Thu
                bình an, vui vẻ, gắn kết và có thật nhiều kỷ niệm đẹp.
            </p>
            <div class="tt26-closing-signature">TRUNG THU 2026 · LLCS LSSD</div>
        </div>
        <div class="tt26-closing-lanterns" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
        </div>
    </section>

    {{-- =========================================================
         IMAGE MODAL
         ========================================================= --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content tt26-modal-content border-0">
                <div class="modal-body p-2">
                    <button type="button" class="tt26-modal-close" data-bs-dismiss="modal" aria-label="Đóng">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <img id="modalImg" src="" class="img-fluid tt26-modal-image" alt="Nội dung LSSD">
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="tt26-top" id="tt26Top" aria-label="Về đầu trang">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <footer class="tt26-footer">
        <span>☾</span>
        <strong>TRUNG THU VIỆT NAM 2026</strong>
        <span>✦</span>
        <small>Designed for LLCS LSSD · Festival UI Mode</small>
    </footer>

</div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('assets/js/home_trungthu2026.js') }}"></script>
@endpush