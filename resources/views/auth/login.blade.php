@extends('layouts.app') {{-- Kế thừa layout --}}

@section('title', 'Đăng nhập | LSSD')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endpush
@section('hide_css')
@endsection
@section('hide_navbar')
@endsection

@section('content')
    <div class="container group-login">
        <div class="login-box">
            <div class="logo-login-page">
                <img src="{{ asset('assets/images/LSSD_HRM.logo2.png') }}" alt="Logo">
            </div>
            <h2>CỤC CẢNH SÁT<br><span>LOS SANTOS COUNTY SHERIFF'S</span><br>GTA5VN</h2>

            <form action="{{ route('login') }}" method="POST" class="form-login">
                @csrf
                <div class="form__group field">
                    <input type="text" class="form__field input-login" placeholder="TÊN ĐĂNG NHẬP" name="username"
                        id="username" required />
                    <label for="username" class="form__label">TÊN ĐĂNG NHẬP</label>
                </div>

                <div class="form__group field" style="margin-top: 20px; position: relative;">
                    <input type="password" class="form__field input-login" placeholder="MẬT KHẨU" name="password"
                        id="password" required />
                    <label for="password" class="form__label">MẬT KHẨU</label>
                    <i class="fa-regular fa-eye toggle-password" id="togglePassword"></i>
                </div>

                <div class="account-option">
                    <span><a href="#" class="note-account-link">Chưa có tài khoản?</a></span>
                </div>
                @if ($errors->has('login'))
                    <p class="text-danger"><i class="fa-solid fa-circle-exclamation text-danger"></i>
                        {{ $errors->first('login') }}</p>
                @endif
                <button type="submit" class="btn-login">ĐĂNG NHẬP</button>
            </form>
            <p class="footer-login-page">© 2025 Designed and developed by @jebsoon</p>
            <p class="version-login-page">version 0.1</p>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        @if(session('success'))
            <script>
                toastr.success("{{ session('success') }}");
            </script>
        @endif
    @endpush
@endsection
@section('hide_footer')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');

            togglePassword.addEventListener('click', function () {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endpush