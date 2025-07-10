<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--  --}}
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.7-dist/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/fontawesome-6.5.0/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css?family=Archivo+Black&display=swap" rel="stylesheet">
    @if (!View::hasSection('hide_css'))
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endif
    @stack('styles')
</head>

<body>
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
    </script>
    <script src="{{ asset('assets/js/notification.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>