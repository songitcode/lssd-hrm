/* ══════════════════════════════════════════════════════════
   payroll.js  —  đồng bộ với pr- CSS classes
   Cải tiến: debounce search, fade-in rows, empty state đẹp,
             highlight từ khoá tìm kiếm, modal spinner nhất quán
══════════════════════════════════════════════════════════ */

/* ── HELPERS ──────────────────────────────────────────── */

/** Debounce: tránh gọi API liên tục mỗi ký tự */
function debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/** Hiển thị / ẩn loading overlay của bảng chính */
function setTableLoading(visible) {
    const loader = document.getElementById('loading-spinner');
    if (!loader) return;
    loader.style.display = visible ? 'flex' : 'none';
}

/** Tô đậm từ khoá trong chuỗi */
function highlight(text, query) {
    if (!query) return text;
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return String(text).replace(
        new RegExp(`(${escaped})`, 'gi'),
        '<mark style="background:var(--pr-amber-dim);color:var(--pr-amber-dark);border-radius:3px;padding:0 2px;">$1</mark>'
    );
}

/** Fade-in các row mới chèn vào tbody */
function animateRows(tbody) {
    tbody.querySelectorAll('tr').forEach(function (tr, i) {
        tr.style.opacity = '0';
        tr.style.transform = 'translateY(8px)';
        tr.style.transition = 'opacity 0.22s ease, transform 0.22s ease';
        setTimeout(function () {
            tr.style.opacity = '1';
            tr.style.transform = 'translateY(0)';
        }, i * 35);
    });
}

/** HTML cho trạng thái "không có kết quả" */
function emptyStateHTML(message) {
    return `
        <tr>
            <td colspan="8" style="padding:2.5rem;text-align:center;color:var(--pr-text-3);">
                <div style="font-size:2rem;margin-bottom:0.5rem;">🔍</div>
                <div style="font-size:0.9rem;font-weight:500;">${message}</div>
            </td>
        </tr>
    `;
}

/** Build 1 row từ object employee (search JSON response) */
function buildRow(emp, index, query) {
    const name = emp.name_ingame ?? emp.username ?? '-';
    const position = emp.position?.name_positions ?? '-';
    const rank = emp.rank?.name_ranks ?? '-';
    const minutes = Math.round((emp.user?.total_hours ?? 0) * 60);
    const hours = emp.user?.total_hours ?? 0;
    const rate = emp.user?.employee?.position?.salary_config?.hourly_rate ?? 24000;
    const wage = emp.user?.total_wage ?? 0;

    return `
        <tr>
            <td class="pr-td pr-td--stt">${index + 1}</td>
            <td class="pr-td pr-td--name">${highlight(name, query)}</td>
            <td class="pr-td">${highlight(position, query)}</td>
            <td class="pr-td">${rank}</td>
            <td class="pr-td pr-td--num">${Number(minutes).toLocaleString()} phút ~ ${Number(hours).toLocaleString()}h</td>
            <td class="pr-td pr-td--rate">${Number(rate).toLocaleString()}$/h</td>
            <td class="pr-td pr-td--wage">${Number(wage).toLocaleString()}$</td>
            <td class="pr-td-action">
                <a href="${emp.attendance_url}" class="pr-btn-view btn_xem_lich_su_cham_cong" target="_parent">
                    Xem <i class="fa-solid fa-eye"></i>
                </a>
            </td>
        </tr>
    `;
}

/* ── SEARCH ───────────────────────────────────────────── */

const searchInput = document.getElementById('search-employee');

if (searchInput) {
    searchInput.addEventListener('input', debounce(function () {
        const query = this.value.trim();
        const tbody = document.querySelector('.pr-tbl tbody');
        if (!tbody) return;

        setTableLoading(true);

        /* Nếu rỗng → fetch lại full tbody từ server */
        if (query === '') {
            fetch('/payroll')
                .then(function (res) { return res.text(); })
                .then(function (html) {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newBody = doc.querySelector('.pr-tbl tbody');
                    if (newBody) {
                        tbody.innerHTML = newBody.innerHTML;
                        animateRows(tbody);
                    }
                    setTableLoading(false);
                })
                .catch(function () { setTableLoading(false); });
            return;
        }

        /* Có nội dung → tìm kiếm JSON */
        fetch(`/payroll/search?query=${encodeURIComponent(query)}`)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                tbody.innerHTML = '';

                if (!data.data || data.data.length === 0) {
                    tbody.innerHTML = emptyStateHTML('Không tìm thấy nhân sự nào phù hợp.');
                    setTableLoading(false);
                    return;
                }

                data.data.forEach(function (emp, index) {
                    tbody.innerHTML += buildRow(emp, index, query);
                });

                animateRows(tbody);
                setTableLoading(false);

                /* Gắn lại event cho các nút Xem mới render */
                tbody.querySelectorAll('.btn_xem_lich_su_cham_cong').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const overlay = document.getElementById('loadingOverlay');
                        if (overlay) overlay.style.display = 'flex';
                    });
                });
            })
            .catch(function () { setTableLoading(false); });

    }, 300)); /* debounce 300ms */
}

/* ── BẢNG LƯƠNG THÁNG TRƯỚC ───────────────────────────── */

const viewPrevBtn = document.getElementById('viewPrevPayroll');

if (viewPrevBtn) {
    viewPrevBtn.addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('previousPayrollModal'));
        const contentDiv = document.getElementById('prevPayrollContent');

        /* Spinner nhất quán với CSS mới */
        contentDiv.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:center;padding:3rem;">
                <div class="pr-spinner"></div>
            </div>
        `;

        fetch(`${window.location.origin}/payroll/previous`)
            .then(function (res) { return res.json(); })
            .then(function (response) {
                const data = response.data;

                if (!data || data.length === 0) {
                    contentDiv.innerHTML = `
                        <div style="text-align:center;padding:2rem;color:var(--pr-text-3);">
                            <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
                            <p style="font-size:0.9rem;">Không có dữ liệu tháng trước.</p>
                        </div>
                    `;
                    return;
                }

                const totalWageAll = data.reduce(function (sum, item) {
                    return sum + Number(item.total_wage);
                }, 0);

                let rowsHTML = '';
                data.forEach(function (item, index) {
                    const name = item.user.employee?.name_ingame ?? item.user.username ?? '-';
                    const pos = item.user.employee?.position?.name_positions || '—';
                    const rank = item.user.employee?.rank?.name_ranks || '—';
                    const rate = item.user.employee?.position?.salary_config?.hourly_rate || 24000;

                    rowsHTML += `
                        <tr>
                            <td class="pr-td pr-td--stt">${index + 1}</td>
                            <td class="pr-td pr-td--name">${name}</td>
                            <td class="pr-td">${pos}</td>
                            <td class="pr-td">${rank}</td>
                            <td class="pr-td pr-td--num">${item.total_hours}h</td>
                            <td class="pr-td pr-td--rate">${Number(rate).toLocaleString()}$/h</td>
                            <td class="pr-td pr-td--wage">${Number(item.total_wage).toLocaleString()}$</td>
                        </tr>
                    `;
                });

                contentDiv.innerHTML = `
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                        <span style="font-size:0.85rem;color:var(--pr-text-2);">
                            Tổng lương: <strong style="color:var(--pr-green);font-size:1rem;">${totalWageAll.toLocaleString()}$</strong>
                        </span>
                        <button id="btnExportPrev" class="pr-btn pr-btn--green">
                            <i class="fa fa-file-excel"></i> Xuất Excel
                        </button>
                    </div>
                    <div style="overflow-x:auto;">
                        <table id="prevPayrollTable" class="pr-tbl table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Chức Vụ</th>
                                    <th>Quân Hàm</th>
                                    <th>Thời Gian</th>
                                    <th>Hệ Số</th>
                                    <th>Tổng Lương</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHTML}</tbody>
                        </table>
                    </div>
                `;

                /* Animate modal rows */
                const modalTbody = contentDiv.querySelector('tbody');
                if (modalTbody) animateRows(modalTbody);

                /* Export button */
                document.getElementById('btnExportPrev').addEventListener('click', function () {
                    exportPayrollDataToExcel(data, 'bang-luong-thang-truoc');
                });
            })
            .catch(function () {
                contentDiv.innerHTML = `
                    <div style="text-align:center;padding:2rem;color:var(--pr-red);">
                        <p>Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.</p>
                    </div>
                `;
            });

        modal.show();
    });
}

/* ── XEM LỊCH SỬ → LOADING OVERLAY ───────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn_xem_lich_su_cham_cong').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'flex';
        });
    });
});

/* ── EXPORT EXCEL TỪ DATA JSON ────────────────────────── */

function exportPayrollDataToExcel(data, filename) {
    filename = filename || 'export';

    let tableHTML = `
        <table border="1">
            <thead>
                <tr>
                    <th>#</th><th>Tên</th><th>Chức Vụ</th><th>Quân Hàm</th>
                    <th>Thời Gian Làm Việc</th><th>Hệ Số</th><th>Tổng Lương</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(function (item, index) {
        const name = item.user.employee?.name_ingame ?? item.user.username ?? '-';
        const pos = item.user.employee?.position?.name_positions || '—';
        const rank = item.user.employee?.rank?.name_ranks || '—';
        const rate = item.user.employee?.position?.salary_config?.hourly_rate || 24000;

        tableHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${name}</td>
                <td>${pos}</td>
                <td>${rank}</td>
                <td>${item.total_hours}h</td>
                <td>${Number(rate).toLocaleString()}$/h</td>
                <td>${Number(item.total_wage).toLocaleString()}</td>
            </tr>
        `;
    });

    const totalWageAll = data.reduce(function (sum, item) {
        return sum + Number(item.total_wage);
    }, 0);

    tableHTML += `
            <tr>
                <td colspan="6" style="text-align:right;font-weight:bold;">Tổng Lương Toàn Bộ:</td>
                <td style="font-weight:bold;">${totalWageAll.toLocaleString()}</td>
            </tr>
        </tbody></table>
    `;

    const blob = new Blob(['\ufeff', tableHTML], { type: 'application/vnd.ms-excel' });
    const downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(blob);
    downloadLink.download = `${filename}.xls`;
    downloadLink.click();
    URL.revokeObjectURL(downloadLink.href);
}