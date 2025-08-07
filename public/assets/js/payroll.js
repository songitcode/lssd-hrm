document.getElementById('search-employee').addEventListener('input', function () {
    const query = this.value.trim();
    const tbody = document.querySelector('.table-employees tbody');
    const loader = document.getElementById('loading-spinner');

    loader.style.display = 'block'; // Hiện loading

    if (query === '') {
        // Nếu rỗng, fetch lại full table HTML
        fetch('/payroll')
            .then(response => response.text())
            .then(html => {
                // Tạo 1 thẻ DOM tạm để lấy nội dung tbody
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.querySelector('.table-employees tbody');

                if (newTbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                }

                loader.style.display = 'none'; // Ẩn loading
            });
        return;
    }

    // Nếu có nội dung -> tìm kiếm bằng JSON
    fetch(`/payroll/search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';

            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center">Không có nhân sự nào.</td></tr>`;
                loader.style.display = 'none';
                return;
            }

            data.data.forEach((emp, index) => {
                const name = emp.name_ingame ?? emp.username ?? '-';
                const position = emp.position?.name_positions ?? '-';
                const rank = emp.rank?.name_ranks ?? '-';
                const minutes = 60 * emp.summary?.total_hours ?? 0;
                const hours = emp.summary?.total_hours ?? 0;
                const rate = emp.position?.salary_config?.hourly_rate ?? 24000;
                const wage = emp.summary?.total_wage?.toLocaleString() ?? '0';

                tbody.innerHTML += `
                    <tr>
                        <td class="hover_1 text-center">${index + 1}</td>
                        <td class="hover_1">${name}</td>
                        <td class="hover_1">${position}</td>
                        <td class="hover_1">${rank}</td>
                        <td class="hover_1">${Number(minutes).toLocaleString()} phút ~ ${hours}h</td>
                        <td class="hover_1">${rate.toLocaleString()}$/h</td>
                        <td class="hover_1">${Number(wage).toLocaleString()}$</td>
                        <td class="text-center history_function">
                            <a href="${emp.attendance_url}" class="btn_xem_lich_su_cham_cong" target="_parent">
                                Xem <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            loader.style.display = 'none';
        });
});

// Bản lương tháng trước
document.getElementById('viewPrevPayroll').addEventListener('click', function () {
    const modal = new bootstrap.Modal(document.getElementById('previousPayrollModal'));
    const contentDiv = document.getElementById('prevPayrollContent');

    contentDiv.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    `;

    fetch(`${window.location.origin}/payroll/previous`)
        .then(res => res.json())
        .then(response => {
            const data = response.data;
            console.log("Dữ liệu tháng trước:", data);
            if (data.length === 0) {
                contentDiv.innerHTML = '<p class="text-danger">Không có dữ liệu tháng trước.</p>';
                return;
            }

            let table = `
                <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Chức Vụ</th>
                            <th>Quân Hàm</th>
                            <th>Phút Làm Việc</th>
                            <th>Hệ Số</th>
                            <th>Tổng Lương</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((item, index) => {
                const name = item.user.employee?.name_ingame ?? item.user.username ?? '-';
                const pos = item.user.employee?.position?.name_positions || '—';
                const rank = item.user.employee?.rank?.name_ranks || '—';
                const rate = item.user.employee?.position?.salary_config?.hourly_rate || 24000;

                table += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${name}</td>
                        <td>${pos}</td>
                        <td>${rank}</td>
                        <td>${item.total_hours}h</td>
                        <td>${Number(rate).toLocaleString()}$/h</td>
                        <td>${Number(item.total_wage).toLocaleString()}$</td>
                    </tr>
                `;
            });

            table += `</tbody></table></div>`;
            const totalWageAll = data.reduce((sum, item) => sum + Number(item.total_wage), 0); // Tổng lương toàn bộ
            table += `
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="6" class="text-end">Tổng Lương Toàn Bộ:</td>
                        <td class="text-success">${totalWageAll.toLocaleString()}$</td>
                    </tr>
                </tfoot>
            `;

            contentDiv.innerHTML = table;
        });

    modal.show();
});
////
