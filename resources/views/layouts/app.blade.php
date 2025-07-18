<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/Logo_LSCSD.png') }}">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- --}}
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.7-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-6.5.0/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
        integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if (!View::hasSection('hide_css'))
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endif
    @stack('styles')
</head>

<body>
    @if (!View::hasSection('hide_navbar'))
        @include('partials.navbar')
    @endif

    <!-- Custom Loading Overlay -->
    <div id="loadingOverlay" class="loader-overlay" style="display: none;">
        <div class="lssd-loader-content">
            <div class="lssd-badge">
                <div class="star"></div>
                <div class="text">LSSD</div>
            </div>
            <div class="loading-text">Loading Los Santos Sheriff Department...</div>
            <div class="loader-clock">
                <span class="hour"></span>
                <span class="min"></span>
                <span class="circel"></span>
            </div>
        </div>
    </div>


    @yield('content')

    <!-- Thông Báo -->
    <div class="notifications">
        <span id="session-success" data-message="{{ session('success') }}"></span>
        <span id="session-warning" data-message="{{ session('warning') }}"></span>
        <span id="session-info" data-message="{{ session('info') }}"></span>
        <!-- <span id="session-warning" data-message="{{ session('error') }}"></span> -->
        <span id="session-error" data-message="{{ $errors->first() }}"></span>
    </div>

    {{-- Footer chỉ hiển thị nếu view không có section hide_footer --}}
    @if (!View::hasSection('hide_footer'))
        @include('partials.footer')
    @endif
    <script>
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

        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-8YDHTMMQJE');
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8YDHTMMQJE"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.min.js" integrity="sha512-zKeerWHHuP3ar7kX2WKBSENzb+GJytFSBL6HrR2nPSR1kOX1qjm+oHooQtbDpDBSITgyl7QXZApvDfDWvKjkUw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="{{ asset('assets/js/loading.js') }}"></script>
    <script src="{{ asset('assets/js/notification.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>