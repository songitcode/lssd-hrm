document.addEventListener('click', function (e) {
    if (!resultsContainer.contains(e.target) && e.target !== searchInput) {
        resultsContainer.style.display = 'none';
    }
});
document.addEventListener("click", function (e) {
    const popup = document.querySelector(".popup");
    const checkbox = popup.querySelector("input[type=checkbox]");

    if (!popup.contains(e.target)) {
        checkbox.checked = false;
    }
});
document.getElementById('selectAllTrash')?.addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});
// Ẩn hiện nút xóa chọn trong trash
document.addEventListener('DOMContentLoaded', function () {
    const btnDelete = document.getElementById('btnDeleteSelected');
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const selectAll = document.getElementById('selectAllTrash');

    function toggleDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        btnDelete.style.display = anyChecked ? 'inline-block' : 'none';
    }

    // Bắt sự kiện từng checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleDeleteButton);
    });

    // Bắt sự kiện checkbox chọn tất cả
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checked = this.checked;
            checkboxes.forEach(cb => cb.checked = checked);
            toggleDeleteButton();
        });
    }

    // Khởi tạo trạng thái nút khi load trang
    toggleDeleteButton();
});
document.querySelector('form#bulkDeleteForm')?.addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert("Vui lòng chọn ít nhất 1 nhân sự để xóa vĩnh viễn.");
    }
});

/////
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Lấy dữ liệu từ data-*
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const username = button.getAttribute('data-username');
        const position = button.getAttribute('data-position');
        const rank = button.getAttribute('data-rank');
        const avatar = button.getAttribute('data-avatar');

        // Gán dữ liệu vào form
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_position_id').value = position;
        document.getElementById('edit_rank_id').value = rank;
        document.getElementById('edit_avatar_preview').src = avatar;

        // Đặt action cho form
        document.getElementById('editUserForm').action = `/employees/${id}`;
    });
});

///// Trở lại modal sửa nhân sự
function goBackToEditModal() {
    const changePasswordModal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
    changePasswordModal.hide();

    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}

const changePasswordModalEl = document.getElementById('changePasswordModal');
changePasswordModalEl.addEventListener('show.bs.modal', function () {
    const id = document.getElementById('edit_id').value;
    const name = document.getElementById('edit_name').value;

    document.getElementById('change_password_id').value = id;
    document.getElementById('changePasswordForm').action = `/employees/change-password/${id}`;
    document.getElementById('changePasswordName').textContent = name;
});

/////
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('changePasswordForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Xóa lỗi cũ
        ['old_password', 'new_password', 'new_password_confirmation'].forEach(field => {
            document.getElementById('error_' + field).innerText = '';
        });

        const formData = new FormData(form);
        const id = document.getElementById('change_password_id').value;
        const url = `/employees/change-password/${id}`;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                // Thành công
                Swal.fire({
                    icon: 'success',
                    title: 'Đổi mật khẩu thành công',
                    text: 'Vui lòng đăng nhập bằng mật khẩu mới',
                    confirmButtonText: 'Đã hiểu',
                    confirmButtonColor: '#3085d6'
                });
                const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                modal.hide();
                form.reset();
            })
            .catch(err => {
                // Nếu là lỗi validation
                if (err.errors) {
                    Object.keys(err.errors).forEach(field => {
                        document.getElementById('error_' + field)?.classList.remove('d-none');
                        document.getElementById('error_' + field).innerText = err.errors[field][0];
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mật khẩu sai',
                        text: 'Mật khẩu cũ không đúng. Vui lòng thử lại!',
                        confirmButtonText: 'Đã hiểu',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    let shown = false;

    document.getElementById('toggleAllPasswords').addEventListener('click', function () {
        shown = !shown;

        const fields = ['old_password', 'new_password', 'new_password_confirmation'];
        fields.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.type = shown ? 'text' : 'password';
            }
        });

        const icon = this.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye', !shown);
            icon.classList.toggle('fa-eye-slash', shown);
        }

        this.innerHTML = `
                <i class="fa-regular ${shown ? 'fa-eye-slash' : 'fa-eye'}"></i> 
                ${shown ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'}
            `;
    });
});