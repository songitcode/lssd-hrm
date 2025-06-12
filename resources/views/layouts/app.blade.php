<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    @if (!View::hasSection('hide_css'))
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endif
    @stack('styles')
</head>

<body>
    {{-- Navbar --}}
    @if (!View::hasSection('hide_navbar'))
        @include('partials.navbar')
    @endif

    @yield('content')

    <!-- Thông Báo -->
    <div class="notifications">
        <span id="session-success" data-message="{{ session('success') }}"></span>
        <span id="session-warning" data-message="{{ session('warning') }}"></span>
        <!-- <span id="session-warning" data-message="{{ session('error') }}"></span> -->
        <span id="session-error" data-message="{{ $errors->first() }}"></span>
    </div>

    {{-- Footer chỉ hiển thị nếu view không có section hide_footer --}}
    @if (!View::hasSection('hide_footer'))
        <div class="container">
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <div class="col-md-4 d-flex align-items-center">
                    <span class="mb-3 mb-md-0 text-body-secondary">
                        © 2025 Designed and developed by @jebsoon - version 0.1
                    </span>
                </div>
            </footer>
        </div>
    @endif
    <script>
        // Tự động ẩn alert sau 3 giây (3000 ms)
        // setTimeout(() => {
        //     const alerts = document.querySelectorAll('.alert');
        //     alerts.forEach(alert => {
        //         // Dùng Bootstrap 5 để đóng alert
        //         const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        //         bsAlert.close();
        //     });
        // }, 3000);

        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Chỉ cho phép ảnh định dạng JPEG, PNG, JPG, GIF');
                    event.target.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('Kích thước ảnh tối đa là 2MB');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
    <script src="{{ asset('assets/js/notification.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>