@extends('layouts.app') {{-- Kế thừa layout --}}

@section('title', 'Đăng nhập')

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
                    <span>
                        <a href="#" class="note-account-link" data-bs-toggle="modal" data-bs-target="#registerModal">Chưa có
                            tài khoản?</a>
                    </span>
                </div>
                @if ($errors->has('login'))
                    <p class="text-danger"><i class="fa-solid fa-circle-exclamation text-danger"></i>
                        {{ $errors->first('login') }}</p>
                @endif
                <button type="submit" class="btn-login">ĐĂNG NHẬP</button>
            </form>
            <p class="footer-login-page">© 2025 Designed and developed by <a style="color: white;"
                    href="https://github.com/songitcode" target="_blank">@jebsoon</a></p>
            <p class="version-login-page">version 0.1</p>
        </div>
    </div>

    <!-- Modal hiển thị thông tin liên thệ khi chưa có tài khoảng -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">
                        <strong>
                            Chưa có tài khoản vui lòng liên hệ Discord
                        </strong>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-white">
                    @if ($contacts->count())
                        <table class="table_login_contact">
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr class="mb-5">
                                        <td>
                                            @if ($contact->employee->avatar)
                                                <img src="{{ asset('storage/' . $contact->employee->avatar) }}" alt="Avatar"
                                                    class="avatar_contact">
                                            @else
                                                <img src="{{ asset('assets/images/user_preview_logo.png') }}" alt="Default"
                                                    class="avatar_contact">
                                            @endif
                                        </td>
                                        <td>
                                            {{--<span style="font-size: 13px;">{{ $contact->employee->rank->name_ranks }}</span>
                                            - --}}
                                            <strong>{{ $contact->employee->name_ingame }}</strong>
                                        </td>
                                        <td>{{ $contact->employee->position->name_positions }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Không tìm thấy thông tin liên hệ.</p>
                    @endif
                </div>
            </div>
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