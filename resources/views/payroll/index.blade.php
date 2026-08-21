@extends('layouts.admin')

@section('title', 'Lương')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payrol_page2.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
            /* ── Cycle badge ───────────────────────────────────── */
            .pr-cycle-badge {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                font-size: .78rem;
                font-weight: 600;
                padding: .28rem .85rem;
                border-radius: 999px;
                letter-spacing: .02em;
                white-space: nowrap;
            }
            .pr-cycle-badge.monthly  { background: #dbeafe; color: #1d4ed8; }
            .pr-cycle-badge.biweekly { background: #d1fae5; color: #065f46; }

            .pr-cycle-btn {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                font-size: .8rem;
                font-weight: 600;
                padding: .35rem 1rem;
                border-radius: 8px;
                border: 1.5px solid #6366f1;
                color: #6366f1;
                background: transparent;
                cursor: pointer;
                transition: background .18s, color .18s;
                white-space: nowrap;
            }
            .pr-cycle-btn:hover { background: #6366f1; color: #fff; }

            /* ── Modal đổi chu kỳ ──────────────────────────────── */
            #cycleModal .modal-content { border-radius: 16px; overflow: hidden; }
            .cycle-opt {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem 1.1rem;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                cursor: pointer;
                transition: border-color .18s, background .18s;
                margin-bottom: .7rem;
            }
            .cycle-opt:hover           { border-color: #a5b4fc; background: #f5f3ff; }
            .cycle-opt.is-active       { border-color: #6366f1; background: #eef2ff; }
            .cycle-opt input[type=radio] { margin-top: .2rem; accent-color: #6366f1; flex-shrink: 0; }
            .cycle-opt-body h6         { font-weight: 700; margin: 0 0 .2rem; font-size: .93rem; }
            .cycle-opt-body small      { color: #6b7280; line-height: 1.4; }
            #biweeklyDateWrap          { margin-top: .65rem; }
        </style>
@endpush

@section('content')
    <div class="pr-page mt-5">
        <div class="pr-wrap">

            {{-- ── TOP BAR ── --}}
            <div class="pr-topbar">
                <div class="pr-topbar-title" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                    <h2 style="margin:0;">Thông Tin Chấm Công &amp; Tiền Lương</h2>

                    {{-- Badge kỳ hiện tại --}}
                    <span class="pr-cycle-badge {{ $config->cycle_type }}">
                        @if($config->cycle_type === 'monthly')
                            <i class="fa-regular fa-calendar"></i> {{ $period['label'] }}
                        @else
                            <i class="fa-solid fa-calendar-week"></i> {{ $period['label'] }}
                        @endif
                    </span>

                    {{-- Nút đổi chu kỳ — chỉ admin / manager --}}
                    @if(auth()->user()->isDownAdminRole())
                        <button class="pr-cycle-btn" data-bs-toggle="modal" data-bs-target="#cycleModal">
                            <i class="fa-solid fa-sliders"></i> Chu Kỳ Lương
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── STAT CARDS ── --}}
            <div class="pr-stats">
                <div class="pr-stat-card pr-stat--amber">
                    <span class="pr-stat-label">
                        @if($config->cycle_type === 'monthly')
                            Tổng lương {{ $period['label'] }}
                        @else
                            Tổng lương kỳ {{ $period['label'] }}
                        @endif
                    </span>
                    <span class="pr-stat-value pr-v--amber">{{ number_format($tongTienLuongThang) }}$</span>
                </div>
                <div class="pr-stat-card pr-stat--blue">
                    <span class="pr-stat-label">Tổng nhân viên</span>
                    <span class="pr-stat-value pr-v--blue">{{ $tongNhanVien }}</span>
                </div>
                <div class="pr-stat-card pr-stat--green">
                    <span class="pr-stat-label">Đã chấm công</span>
                    <span class="pr-stat-value pr-v--green">{{ $tongNhanVienDaChamCong }}</span>
                </div>
            </div>

            {{-- ── TABLE CARD ── --}}
            <div class="pr-table-card">

                {{-- Toolbar --}}
                <div class="pr-toolbar">
                    <div class="pr-search-wrap">
                        <i class="fas fa-search pr-search-ico"></i>
                        <input type="text" id="search-employee" class="pr-search-input"
                            placeholder="Tìm tên sĩ quan hoặc tên đăng nhập...">
                    </div>
                    {{--@if(auth()->user()->isDownAdminRole())
                        <form action="{{ route('attendance.resetAttendanceDta') }}" method="POST"
                            onsubmit="return confirm('WARNING!! Bạn có chắc chắn muốn xóa toàn bộ dữ liệu chấm công? sẽ không khôi phục được dữ liệu')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pr-btn pr-btn--red-outline" id="resetButon">
                                <i class="fa fa-trash"></i> Reset Toàn Bộ Dữ Liệu
                            </button>
                        </form>
                    @endif--}}
                    <button class="pr-btn pr-btn--green"
                        onclick="exportTableToExcel('payrollTable', 'bang-luong-{{ Str::slug($period['label']) }}')">
                        <i class="fa fa-file-excel"></i> Xuất Excel
                    </button>
                    <button class="pr-btn pr-btn--blue" id="viewPrevPayroll">
                        <i class="fa-solid fa-chart-column"></i>
                        Kỳ trước ({{ $period['label_prev'] }})
                    </button>
                </div>

                {{-- Loading overlay --}}
                <div id="loading-spinner" class="pr-spinner-wrap">
                    <div class="pr-spinner"></div>
                </div>

                {{-- Table --}}
                <div class="pr-tbl-wrap">
                    <table id="payrollTable" class="pr-tbl table-bordered">
                        <thead>
                            <tr>
                                <th class="pr-th--center">STT</th>
                                <th>Tên Sĩ Quan</th>
                                <th>Chức Vụ</th>
                                <th>Quân Hàm</th>
                                <th>Phút ~ Giờ Làm Việc</th>
                                <th>Hệ Số Lương</th>
                                <th>
                                    @if($config->cycle_type === 'monthly')
                                        Tổng Lương Tháng
                                    @else
                                        Tổng Lương 2 Tuần
                                    @endif
                                </th>
                                <th class="pr-th--center">Lịch Sử Chấm Công</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stt = 1; @endphp
                            @foreach ($users as $user)
                                @php
                                    $positionName = $user->employee->position->name_positions ?? '';
                                    if (in_array($positionName, ['Cục Trưởng', 'Phó Cục Trưởng']))
                                        continue;

                                    $userID = (int) $user->id;
                                    $summary = $summaries[$userID] ?? null;
                                    $minutesWorked = $summary ? $summary->total_minutes : 0;
                                    $hoursWorked = $summary ? $summary->total_hours : 0;
                                    $wage = $summary ? number_format($summary->total_wage) : 0;
                                    $rate = $user->effectiveSalaryRate();
                                @endphp
                                <tr>
                                    <td class="pr-td pr-td--stt">{{ $stt++ }}</td>
                                    <td class="pr-td pr-td--name">{{ $user->employee->name_ingame ?? $user->username }}</td>
                                    <td class="pr-td">{{ $user->employee->position->name_positions ?? '—' }}</td>
                                    <td class="pr-td">{{ $user->employee->rank->name_ranks ?? '—' }}</td>
                                    <td class="pr-td pr-td--num">{{ $minutesWorked }} phút ~ {{ $hoursWorked }}h</td>
                                    <td class="pr-td pr-td--rate">{{ number_format($rate) }}$/h</td>
                                    <td class="pr-td pr-td--wage">{{ $wage }}$</td>
                                    <td class="pr-td-action">
                                        <a href="{{ route('payroll.user_attendance', $user) }}" target="_parent"
                                            class="pr-btn-view btn_xem_lich_su_cham_cong">
                                            Xem <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── MODAL bảng lương kỳ trước ── --}}
            <div class="modal fade" id="previousPayrollModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content pr-modal-content">
                        <div class="modal-header pr-modal-header">
                            <h5 class="modal-title" id="prevModalTitle">
                                Bảng Lương Kỳ Trước
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <p class="pr-modal-subtitle">Xem lại bảng lương kỳ trước để dễ dàng thống kê</p>
                        <div class="modal-body" id="prevPayrollContent">
                            <div style="text-align:center;padding:2rem;">
                                <div class="pr-spinner" style="margin:auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── MODAL đổi chu kỳ lương ── --}}
            @if(auth()->user()->isDownAdminRole())
                <div class="modal fade" id="cycleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">
                                    <i class="fa-solid fa-sliders me-2 text-indigo-600" style="color:#6366f1;"></i>
                                    Chu Kỳ Tính Lương
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-2">
                                <p class="text-muted small mb-3">
                                    Thay đổi sẽ áp dụng ngay cho bảng lương hiển thị.
                                    Lịch sử các kỳ cũ vẫn được giữ nguyên.
                                </p>

                                {{-- Option 1: Theo tháng --}}
                                <label class="cycle-opt {{ $config->cycle_type === 'monthly' ? 'is-active' : '' }}" id="opt-monthly">
                                    <input type="radio" name="cycle_type" value="monthly"
                                        {{ $config->cycle_type === 'monthly' ? 'checked' : '' }}>
                                    <div class="cycle-opt-body">
                                        <h6><i class="fa-regular fa-calendar me-1"></i> Theo Tháng</h6>
                                        <small>Tổng hợp từ ngày 1 đến cuối tháng theo lịch.</small>
                                    </div>
                                </label>

                                {{-- Option 2: Theo 2 tuần --}}
                                <label class="cycle-opt {{ $config->cycle_type === 'biweekly' ? 'is-active' : '' }}" id="opt-biweekly">
                                    <input type="radio" name="cycle_type" value="biweekly"
                                        {{ $config->cycle_type === 'biweekly' ? 'checked' : '' }}>
                                    <div class="cycle-opt-body">
                                        <h6><i class="fa-solid fa-calendar-week me-1"></i> Theo 2 Tuần (14 ngày)</h6>
                                        <small>Mỗi kỳ là 14 ngày liên tiếp, bắt đầu từ thứ Hai.</small>

                                        <div id="biweeklyDateWrap"
                                            style="{{ $config->cycle_type !== 'biweekly' ? 'display:none' : '' }}">
                                            <label class="form-label mt-2 mb-1 small fw-semibold">
                                                Chọn ngày thứ Hai đầu tiên làm mốc:
                                            </label>
                                            <input type="date" id="biweeklyRefDate" class="form-control form-control-sm"
                                                value="{{ $config->biweekly_reference_date?->toDateString() }}">
                                            <div class="text-danger small mt-1 d-none" id="dateErrMsg">
                                                Vui lòng chọn đúng ngày thứ Hai.
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <div id="cycleErrMsg" class="alert alert-danger small py-2 d-none mt-2 mb-0"></div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-primary btn-sm" id="saveCycleBtn">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cài Đặt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/payroll.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Loading khi xem lịch sử ──────────────────────────────────
            document.querySelectorAll('.btn_xem_lich_su_cham_cong').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('loadingOverlay').style.display = 'flex';
                });
            });

            // ── Xóa bảng lương kỳ trước ──────────────────────────────────
            document.getElementById('deletePrevPayrollForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Bạn có chắc muốn xóa?',
                    text: 'Toàn bộ bảng lương kỳ trước sẽ bị xóa!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Vâng, xóa đi!',
                    cancelButtonText: 'Hủy'
                }).then(r => {
                    if (!r.isConfirmed) return;
                    fetch("{{ route('payroll.previous.delete') }}", {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(d => {
                            Swal.fire({ icon: 'success', title: 'Đã xóa', text: d.message, confirmButtonColor: '#3085d6' });
                            document.querySelector('#previousPayrollModal .modal-body').innerHTML =
                                '<p style="color:#059669">Đã xóa thành công.</p>';
                        });
                });
            });

            // ── Modal đổi chu kỳ (chỉ render khi có quyền) ───────────────
            @if(auth()->user()->isDownAdminRole())
                const radioMonthly  = document.querySelector('input[name=cycle_type][value=monthly]');
                const radioBiweekly = document.querySelector('input[name=cycle_type][value=biweekly]');
                const dateWrap      = document.getElementById('biweeklyDateWrap');
                const refDateInput  = document.getElementById('biweeklyRefDate');
                const dateErrMsg    = document.getElementById('dateErrMsg');
                const cycleErrMsg   = document.getElementById('cycleErrMsg');
                const saveCycleBtn  = document.getElementById('saveCycleBtn');

                function syncCycleUI() {
                    document.getElementById('opt-monthly').classList.toggle('is-active', radioMonthly.checked);
                    document.getElementById('opt-biweekly').classList.toggle('is-active', radioBiweekly.checked);
                    dateWrap.style.display = radioBiweekly.checked ? '' : 'none';
                    dateErrMsg.classList.add('d-none');
                    cycleErrMsg.classList.add('d-none');
                }
                radioMonthly.addEventListener('change', syncCycleUI);
                radioBiweekly.addEventListener('change', syncCycleUI);

                // Validate thứ Hai
                refDateInput.addEventListener('change', function () {
                    const d = new Date(this.value + 'T00:00:00');
                    dateErrMsg.classList.toggle('d-none', d.getDay() === 1);
                });

                saveCycleBtn.addEventListener('click', async () => {
                    cycleErrMsg.classList.add('d-none');

                    const cycleType = document.querySelector('input[name=cycle_type]:checked').value;
                    const refDate   = refDateInput.value || null;

                    if (cycleType === 'biweekly') {
                        if (!refDate) { dateErrMsg.classList.remove('d-none'); return; }
                        if (new Date(refDate + 'T00:00:00').getDay() !== 1) {
                            dateErrMsg.classList.remove('d-none'); return;
                        }
                    }

                    saveCycleBtn.disabled     = true;
                    saveCycleBtn.innerHTML    = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu…';

                    try {
                        const res  = await fetch("{{ route('payroll.cycle.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                cycle_type: cycleType,
                                biweekly_reference_date: cycleType === 'biweekly' ? refDate : null,
                            })
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            const msg = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : (data.message || 'Lỗi không xác định');
                            cycleErrMsg.textContent = msg;
                            cycleErrMsg.classList.remove('d-none');
                            return;
                        }

                        await Swal.fire({
                            icon: 'success',
                            title: 'Đã lưu!',
                            text: data.message + ' — Kỳ hiện tại: ' + data.period,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        location.reload();

                    } catch {
                        cycleErrMsg.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
                        cycleErrMsg.classList.remove('d-none');
                    } finally {
                        saveCycleBtn.disabled  = false;
                        saveCycleBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cài Đặt';
                    }
                });
            @endif

        });

        // ── Xuất Excel ──────────────────────────────────────────────────────
        function exportTableToExcel(tableId, filename) {
            var table = document.getElementById(tableId);
            if (!table) { alert('Không tìm thấy bảng với ID: ' + tableId); return; }
            var data = [];
            var headers = [];
            table.querySelectorAll('thead tr th').forEach(th => headers.push(th.innerText.trim()));
            data.push(headers);
            table.querySelectorAll('tbody tr').forEach(tr => {
                var row = [];
                tr.querySelectorAll('td').forEach(td => row.push(td.innerText.trim()));
                if (row.length) data.push(row);
            });
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, (filename || 'data') + '.xlsx');
        }
    </script>
@endpush
