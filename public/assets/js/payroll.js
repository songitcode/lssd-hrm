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
                const minutes = emp.summary?.total_minutes ?? 0;
                const hours = emp.summary?.total_hours ?? 0;
                const rate = emp.effective_rate ?? 0;
                const wage = emp.summary?.total_wage?.toLocaleString() ?? '0';

                tbody.innerHTML += `
                    <tr>
                        <td class="hover_1 text-center">${index + 1}</td>
                        <td class="hover_1">${name}</td>
                        <td class="hover_1">${position}</td>
                        <td class="hover_1">${rank}</td>
                        <td class="hover_1">${minutes} phút ~ ${hours}h</td>
                        <td class="hover_1">${rate.toLocaleString()}$/h</td>
                        <td class="hover_1">${wage}$</td>
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