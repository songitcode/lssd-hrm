@extends('layouts.app')

@section('title', 'OnDuty Live')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/onduty_page.css') }}">
    <style>
        .blink-danger {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.75;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="group-function row">
            <div class="col-lg-6">
                <h3>Danh sách On-Duty Real-time</h3>
            </div>
        </div>
        <div class="table-responsive box-employees text-center">
            <table class="table table-bordered table-hover-custom align-items-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID_EM</th>
                        <th>Tên</th>
                        <th>Chức vụ</th>
                        <th>Quân hàm</th>
                        <th>Date</th>
                        <th>Giờ bắt đầu</th>
                        <th>Thời gian</th>
                        <th>Discord</th>
                    </tr>
                </thead>
                <tbody id="onduty-body">
                    <tr>
                        <td colspan="9">Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        let cache = "";
        let oldIds = new Set();

        // ======================
        // FORMAT TIME
        // ======================
        function format(sec) {
            const h = String(Math.floor(sec / 3600)).padStart(2, '0');
            const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        // ======================
        // TIMER
        // ======================
        function updateTimers() {
            const now = Math.floor(Date.now() / 1000);

            document.querySelectorAll('.timer').forEach(el => {
                const checkin = Number(el.dataset.time);
                if (!checkin) return;

                let diff = now - checkin;
                if (diff < 0) diff = 0;

                el.textContent = format(diff);
            });
        }

        setInterval(updateTimers, 1000);

        function isToday(dateString) {
            let d = new Date(dateString);
            let now = new Date();

            return (
                d.getDate() === now.getDate() &&
                d.getMonth() === now.getMonth() &&
                d.getFullYear() === now.getFullYear()
            );
        }
        function getRowStatus(checkIn) {
            let d = new Date(checkIn);
            let now = new Date();

            // check có phải hôm nay không
            let isToday =
                d.getDate() === now.getDate() &&
                d.getMonth() === now.getMonth() &&
                d.getFullYear() === now.getFullYear();

            // tính số giờ làm
            let diffSec = Math.floor((now - d) / 1000);
            let hours = diffSec / 3600;

            if (hours >= 10) {
                return {
                    row: 'table-danger blink-danger',
                    cell: 'bg-danger text-white'
                };
            }

            if (hours >= 4) {
                return {
                    row: 'table-warning',
                    cell: 'bg-warning'
                };
            }

            return {
                row: '',
                cell: ''
            };
        }
        // ======================
        // DISCORD RENDER
        // ======================
        function formatElapsed(startMs) {
            let start = Math.floor(startMs / 1000);
            let now = Math.floor(Date.now() / 1000);

            let diff = now - start;
            if (diff < 0) diff = 0;

            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;

            return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
        function renderDiscord(activities) {
            if (!activities || activities.length === 0) {
                return `<small class="text-muted">Chưa tham gia hoặc không có hoạt động</small>`;
            }

            let html = "";

            activities.forEach(activity => {

                if (activity.assets?.large_image) {
                    let img = `https://cdn.discordapp.com/app-assets/${activity.application_id}/${activity.assets.large_image}.png`;

                    html += `<img src="${img}" width="40" style="border-radius:8px;">`;
                }

                // bỏ custom status
                if (activity.type === 4) return;

                html += `
                                    <small>
                                        <span class="text-primary">${activity.name}</span><br>
                                        ${activity.details ? `<div>${activity.details}</div>` : ''}
                                        ${activity.timestamps?.start
                        ? `<span class="text-success">${formatElapsed(activity.timestamps.start)}</span>` : ''}
                                    </small>
                                `;

                // CHI TIẾT
                html += `
                                    <details style="padding:6px; border-radius:10px; background:#e8a800; color:#fff; margin-top:5px;">
                                        <summary><small>Chi tiết</small></summary>
                                        <small>
                                            ${activity.state ? `<div><b>Trạng thái:</b> ${activity.state}</div>` : ''}
                                            ${activity.assets?.large_text ? `<div><b>Mô tả:</b> ${activity.assets.large_text}</div>` : ''}
                                            ${activity.platform ? `<div><b>Nền tảng:</b> ${activity.platform}</div>` : ''}
                                        </small>
                                    </details>
                                `;
            });

            return html || `<small class="text-muted">Không có hoạt động</small>`;
        }

        // ======================
        // RENDER TABLE
        // ======================
        function render(data) {
            let tbody = document.getElementById('onduty-body');

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="9">Không có ai OnDuty</td></tr>`;
                return;
            }

            let html = "";
            let newIds = new Set();

            data.forEach((item, i) => {
                let d = new Date(item.check_in);
                let status = getRowStatus(item.check_in);
                let id = item.id;

                let today = isToday(item.check_in);

                newIds.add(id);

                let isNew = !oldIds.has(id);

                html += `
                        <tr class="${status.row} align-middle ${isNew ? 'table-success' : ''}">
                            <td>${i + 1}</td>
                            <td>${item.user.employee.id}</td>
                            <td>${item.user.employee.name_ingame ?? '-'}</td>
                            <td>${item.user.employee.position?.name_positions ?? '-'}</td>
                            <td>${item.user.employee.rank?.name_ranks ?? '-'}</td>
                            <td>${d.toLocaleDateString()}</td>
                            <td class="${status.cell}">${d.toLocaleTimeString('en-GB')}</td>
                            <td class="${status.cell}">
                                <span class="timer text-success" data-time="${Math.floor(d.getTime() / 1000)}">
                                    00:00:00
                                </span>
                            </td>
                            <td>
                                ${renderDiscord(item.discord)}
                            </td>
                        </tr>
                    `;
            });

            tbody.innerHTML = html;

            updateTimers();

            // remove highlight new user after 7s
            setTimeout(() => {
                document.querySelectorAll('.table-success').forEach(el => {
                    el.classList.remove('table-success');
                });
            }, 7000);

            oldIds = newIds;
        }

        // ======================
        // FETCH
        // ======================
        function loadData() {
            fetch('/api/onduty')
                .then(res => res.json())
                .then(data => {
                    let json = JSON.stringify(data);

                    if (json !== cache) {
                        cache = json;
                        render(data);
                    }
                })
                .catch(err => console.error(err));
        }

        // ======================
        // INIT
        // ======================
        loadData();
        setInterval(loadData, 3500);
    </script>
@endsection