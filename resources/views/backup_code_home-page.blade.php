<!-- BACKUP SOURCE EVENT -->

<div class="event-backup">
    <div class="group-event-home my-5">
        <h3><small><b>Vinh danh các Sĩ Quan có thành tích xuất sắc sự kiện “BE THE WATCH – BRING THE PEACE - 2025”
                </b></small></h3>
        <div class="row">
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
    <div class="group-event-home my-5 text-center">
        <h3><small><b>Vinh Danh Đại Hội Võ Lâm 2025</b></small></h3>
        <div class="d-flex gap-3 justify-content-center">
            <div class="card-event-home">
                <div class="image-container">
                    <img src="{{ asset('assets/images/dai_hoi_vo_lam_1.png') }}" alt="" class="poster-card-home">
                    <div class="event-date">
                        <div class="date-month">Đại hội võ lâm đạt Top 4</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="group-event-home my-5">
        <h3><small><b>SỰ KIỆN Tháng 11</b></small></h3>
        <div class="d-flex gap-3">

            <div class="card-event-home">
                <div class="image-container">
                    <img src="{{ asset('assets/images/poster_gold_voice_lssd_KhongNgay.gif') }}" alt=""
                        class="poster-card-home">
                    <div class="event-tag">Sắp diễn ra</div>
                    <div class="event-date">
                        <div class="date-day">0</div>
                        <div class="date-month">Tháng 0</div>
                    </div>
                    <div class="event-info">
                        <div class="event-meta">
                            <h3 class="event-title">GOLD VOICE LSSD</h3>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-clock"></i></span>
                                <span>--:00 - --:00</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-map-pin fa-xl"></i></span>
                                <span>Discord Ban Ngành LSSD</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-users"></i></span>
                                <span>Toàn bộ nhân sự LSSD tham dự</span>
                            </div>
                            <p class="event-description ">Sự kiện giao lưu âm nhạc của cục cảnh sát LSSD, tụ hợp và giao
                                lưu âm nhạc lựa chọn ra người
                                có Gold Voice để trao thưởng!!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-event-home">
                <div class="image-container">
                    <img src="{{ asset('assets/images/poster_success_or_failure.gif') }}" alt=""
                        class="poster-card-home">
                    <div class="event-tag">Sắp diễn ra</div>
                    <div class="event-date">
                        <div class="date-day">0</div>
                        <div class="date-month">Tháng 0</div>
                    </div>
                    <div class="event-info">
                        <div class="event-meta">
                            <h3 class="event-title">SUCCESS or FAILURE</h3>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-clock"></i></span>
                                <span>--:00 - --:00</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-map-pin fa-xl"></i></span>
                                <span>Discord Ban Ngành LSSD</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-users"></i></span>
                                <span>Toàn bộ nhân sự LSSD tham dự</span>
                            </div>
                            <p class="event-description ">Sự kiện giao lưu đấu súng cùng anh em ban ngành Lực Lượng Cảnh
                                Sát LSSD, chọn tình anh em
                                hay
                                là làm kẻ chiến thắng để dành được giải thưởng !!!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-event-home">
                <div class="image-container">
                    <img src="{{ asset('assets/images/poster_bethewatch-bringthepeace.gif') }}" alt=""
                        class="poster-card-home">
                    <div class="event-date">
                        <div class="date-day">20</div>
                        <div class="date-month">Tháng 11</div>
                    </div>
                    <div class="event-info">
                        <div class="event-meta">
                            <h3 class="event-title">Be The Watch - Bring The Peace</h3>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-clock"></i></span>
                                <span>00:00 <small>20/11/25</small> - 00:00 <small>19/12/25</small></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-map-pin fa-xl"></i></span>
                                <span>Discord Ban Ngành LSSD</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon text-info"><i class="fa-solid fa-users"></i></span>
                                <span>Toàn bộ nhân sự LSSD tham dự</span>
                            </div>
                            <p class="event-description ">Cùng nhau chung tay bảo vệ hòa bình cho thành phố !!!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>