@extends('layouts.app')

@section('title', 'Live Onduty')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/onduty_page.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="group-function row">
            <div class="col-lg-6">
                <h3>Danh sách On-Duty - Cấp Cao Quản Lý</h3>
            </div>
            <p class="text mb-1">Nhấn F5 để xem những nhân sự nào đang trực tiếp nhấn On-Duty</p>
        </div>
        <div class="table-responsive box-employees text-center">
            <table class="table table-bordered table-hover-custom align-items-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID_EM</th>
                        <th>Tên Ingame</th>
                        <th>Chức vụ</th>
                        <th>Quân hàm</th>
                        <th>Date</th>
                        <th>Giờ bắt đầu</th>
                        <th>Trạng thái</th>
                        <th>Hoạt động Discord</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($onDutyList as $att)
                        @php
                            // BACKUP
                            // $response = Http::get("https://api.lanyard.rest/v1/users/$discordId");
                            // $data = $response->json()['data'] ?? [];
                            // 
                            $discordId = $att->user->employee->discord_id ?? 0;
                            $response = Cache::remember("discord_$discordId", 10, function () use ($discordId) {
                                return Http::get("https://api.lanyard.rest/v1/users/$discordId")->json();
                            });

                            $data = $response['data'] ?? [];
                            $activities = $data['activities'] ?? [];
                            if (!function_exists('formatElapsed')) {
                                function formatElapsed($startTimestampMs)
                                {
                                    $start = \Carbon\Carbon::createFromTimestamp($startTimestampMs / 1000);

                                    $seconds = $start->diffInSeconds(\Carbon\Carbon::now());

                                    $hours = floor($seconds / 3600);
                                    $minutes = floor(($seconds % 3600) / 60);
                                    $secs = $seconds % 60;

                                    return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
                                }
                            }
                        @endphp
                        @php
                            $isToday = \Carbon\Carbon::parse($att->check_in)->isToday();
                        @endphp
                        <tr class="{{ $isToday ? '' : 'table-danger' }} align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $att->user->employee->id }}</td>
                            <td>{{ $att->user->employee->name_ingame ?? '-' }}</td>
                            <td>{{ $att->user->employee->position->name_positions ?? '-' }}</td>
                            <td>{{ $att->user->employee->rank->name_ranks ?? '-' }}</td>
                            <td class="{{ $isToday ? '' : 'bg-danger text-white' }}">
                                {{ \Carbon\Carbon::parse($att->check_in)->format('d/m/y') }}
                            </td>
                            <td class="{{ $isToday ? '' : 'bg-danger text-white' }}">
                                {{ \Carbon\Carbon::parse($att->check_in)->format('H:i:s') }}
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $att->status }}
                                </span>
                                <div class="text-secondary">
                                    <small>
                                        @if($att->check_out === null)
                                            <span class="live-timer" data-checkin="{{ strtotime($att->check_in) }}"
                                                id="timer-{{ $att->id }}">
                                                00:00:00
                                            </span>
                                        @else
                                            <span>{{ gmdate('H:i:s', strtotime($att->check_out) - strtotime($att->check_in)) }}</span>
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                @if (empty($activities))
                                    <small>
                                        <span class="text-muted">Chưa tham gia <br> hoặc không tìm thấy hoạt động</span>
                                    </small>
                                @elseif ($discordId == 0)
                                    <small>
                                        <span class="text-muted">Chưa tham gia hoặc liên kết discord</span>
                                    </small>
                                @else
                                    @foreach ($activities as $activity)
                                        @if ($activity['type'] === 4)
                                        @else
                                            <small>
                                                <span class="text-primary">{{ $activity['name'] }}</span><br>
                                                @if (!empty($activity['details']))
                                                    <div>{{ $activity['details'] }}</div>
                                                @endif
                                                @if (!empty($activity['timestamps']['start']))
                                                    @php
                                                        $elapsed = formatElapsed($activity['timestamps']['start']) ?? '';
                                                    @endphp
                                                    <span class="text-success">{{ $elapsed ?? ''}}</span>
                                                @endif
                                            </small>
                                        @endif
                                    @endforeach
                                    <details class="p-2" style="border-radius: 10px; background: #e8a800; color: #fff;">
                                        <summary><small>Chi tiết</small></summary>
                                        <small>
                                            @if (!empty($activity['state']))
                                                <div><b>Trạng thái: </b>{{ $activity['state'] }}</div>
                                            @endif

                                            @if (!empty($activity['assets']['large_text']))
                                                <div><b>Mô tả:</b> {{ $activity['assets']['large_text'] }}</div>
                                            @endif

                                            @if (!empty($activity['platform']))
                                                <div><b>Nền tảng:</b> {{ $activity['platform'] }}</div>
                                            @endif
                                        </small>
                                    </details>
                                @endif
                            </td>
                            <td class="profile_function">
                                @if(auth()->user()->isManager())
                                    @if(auth()->id() !== $att->user->employee->user_id)
                                        <a href="{{ route('payroll.user_attendance', $att->user) }}"
                                            class="me-1 btn_xem_lich_su_cham_cong" target="_blank">
                                            Chi Tiết
                                        </a>
                                        <form action="{{ route('attendance.force_checkout', $att->id) }}" method="POST"
                                            class="force-checkout-form" data-name="{{ $att->user->employee->name_ingame }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn_ket_thuc_ca">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Cảnh Báo
                                            </button>
                                        </form>
                                        <form action="{{ route('attendance.huyCheckin', $att->id) }}" method="POST"
                                            class="force-checkout-form" data-name="{{ $att->user->employee->name_ingame }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn_xoa_ca">
                                                <i class="fa-solid fa-trash"></i> Xóa Onduty
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('attendance.index') }}" class="me-1 btn_xem_lich_su_cham_cong"
                                            target="_blank">
                                            Xem
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Không có ai đang On-Duty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            function format(sec) {
                const h = String(Math.floor(sec / 3600)).padStart(2, '0');
                const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                const s = String(sec % 60).padStart(2, '0');
                return `${h}:${m}:${s}`;
            }

            function updateAllTimers() {
                const now = Math.floor(Date.now() / 1000);

                document.querySelectorAll('.live-timer').forEach(el => {
                    const checkIn = Number(el.dataset.checkin);
                    if (!checkIn) return;

                    let elapsed = now - checkIn;
                    if (elapsed < 0) elapsed = 0;

                    el.textContent = format(elapsed);
                });
            }

            // chạy mỗi giây
            setInterval(updateAllTimers, 1000);

            // cập nhật ngay lập tức lần đầu
            updateAllTimers();
        });
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.force-checkout-form .btn_ket_thuc_ca').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault(); // chặn submit mặc định

                    let form = this.closest('form');
                    let name = form.dataset.name;

                    Swal.fire({
                        title: 'Xác nhận?',
                        text: `Bạn có chắc muốn kết thúc ca của ${name} không?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Có, kết thúc!',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.force-checkout-form .btn_xoa_ca').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault(); // chặn submit mặc định

                    let form = this.closest('form');
                    let name = form.dataset.name;

                    Swal.fire({
                        title: 'Xác nhận?',
                        text: `Onduty của ${name} sẽ bị xóa khỏi hệ thống và không tính lương cho ca làm này. Có xóa không?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Có, xóa nó đi!',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection