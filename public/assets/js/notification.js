document.addEventListener('DOMContentLoaded', function () {
    const successMessage = document.getElementById('session-success').dataset.message;
    const warningMessage = document.getElementById('session-warning').dataset.message;
    const errorMessage = document.getElementById('session-error').dataset.message;

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: successMessage,
            confirmButtonColor: '#28a745'
        });
    }

    if (warningMessage) {
        Swal.fire({
            icon: 'warning',
            title: 'Cảnh báo',
            text: warningMessage,
            confirmButtonColor: '#ffc107'
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: errorMessage,
            confirmButtonColor: '#dc3545'
        });
    }
});