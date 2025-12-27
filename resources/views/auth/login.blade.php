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
                <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="Logo">
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
                        <table class="table_login_contact text-white">
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr class="mb-5">
                                        <td>
                                            @if ($contact->employee->discord_id)
                                                <img src="{{ $contact->employee->discord_avatar }}" alt="Avatar" class="avatar_contact">
                                            @else
                                                @if ($contact->employee->avatar)
                                                    <img src="{{ asset('storage/' . $contact->employee->avatar) }}" alt="Avatar"
                                                        class="avatar_contact">
                                                @else
                                                    <img src="{{ asset('assets/images/user_preview_logo.png') }}" alt="Default"
                                                        class="avatar_contact">
                                                @endif
                                            @endif
                                        </td>
                                        <td>
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

@endsection

@section('hide_footer')
@endsection
<style>

</style>
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @if(session('success'))
        <script>
            toastr.success("{{ session('success') }}");
        </script>
    @endif
    @if(session('error'))
        <div class="notifi">
            {{ session('error') }}
        </div>
    @endif
    <script>
        setTimeout(() => {
            const notifi = document.querySelector('.notifi');
            if (notifi) {
                notifi.remove();
            }
        }, 5000);

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

        document.querySelector('.form-login').addEventListener('submit', function (e) {
            const btn = document.querySelector('.btn-login');
            btn.disabled = true;
            btn.textContent = 'Đang đăng nhập...';
            showLoading();
        });
    </script>
    <script src="{{ asset('assets/js/loading.js') }}"></script>
@endpush
{{--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body {
        margin: 0;
        width: 100vw;
        overflow: hidden;

        color: white !important;
        font-family: system-ui;
        font-size: 1.5vmin;

        background: radial-gradient(circle at bottom, navy 0, black 100%);
    }

    :root {
        --ae: 30vmin;
    }

    .system {
        width: 100vw;
        height: 100vh;
        position: relative;
        background: rgba(128, 0, 128, 0.1) center / 200px 200px round;
    }

    .sun {
        --size: 15vmin;
        width: 100%;
        height: 100%;

        &::after {
            z-index: -1;
            content: "";
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            width: var(--size);
            height: var(--size);
            transform: translate(-50%, -50%);
            background: orange;
            border-radius: calc(infinity * 1px);
        }
    }

    @property --x {
        syntax: "<length>";
        inherits: false;
        initial-value: 0px;
    }

    @property --y {
        syntax: "<length>";
        inherits: false;
        initial-value: 0px;
    }

    @property --angle {
        syntax: "<angle>";
        inherits: false;
        initial-value: 0deg;
    }

    .mercury {
        --size: 1vmin;
        --radius: calc(0.4 * var(--ae));
        --speed: calc(88 / 365);
        background-color: gray;
        border-radius: calc(infinity * 1px);

        &::after {
            content: "Mercury";
            position: absolute;
            left: calc(var(--size) + 0.5vmin);
            top: calc(var(--size) - 0.5vmin);
        }
    }

    .venus {
        --size: 2vmin;
        --radius: calc(0.72 * var(--ae));
        --speed: calc(225 / 365);
        background-color: orangered;
        border-radius: calc(infinity * 1px);

        &::after {
            content: "Venus";
            position: absolute;
            left: var(--size);
            top: calc(-1.2 * var(--size));
        }
    }

    .earth {
        --size: 2vmin;
        --radius: calc(var(--ae) * 1);
        --speed: 1;
        background-color: blue;
        border-radius: calc(infinity * 1px);

        &::after {
            content: "Trái Đất Đang Bảo Trì";
            position: absolute;
            left: var(--size);
            top: calc(-1.2 * var(--size));
        }
    }

    .mars {
        --size: 1.5vmin;
        --radius: calc(1.52 * var(--ae));
        --speed: calc(687 / 365);
        background-color: red;
        border-radius: calc(infinity * 1px);

        &::after {
            content: "Mars";
            position: absolute;
            left: var(--size);
            top: calc(-1.2 * var(--size));
        }
    }

    .moon {
        --size: 0.5vmin;
        --radius: 3vmin;
        --speed: calc(1 / 365);
        background-color: white;
        border-radius: calc(infinity * 1px);
    }

    .spin {
        --x: calc(cos(var(--angle)) * var(--radius) - var(--size) / 2);
        --y: calc(sin(var(--angle)) * var(--radius) - var(--size) / 2);
        position: absolute;
        top: 50%;
        left: 50%;
        width: var(--size);
        height: var(--size);
        translate: calc(var(--x)) calc(var(--y));
        animation: spin linear calc(var(--speed) * 100s) infinite;
    }

    @keyframes spin {
        from {
            --angle: 0turn;
        }

        to {
            --angle: 1turn;
        }
    }
</style>

<body>
    <div class="system">
        <div class="sun">
            <div class="mercury spin"></div>
            <div class="venus spin"></div>
            <div class="earth spin">
                <div class="moon spin"></div>
            </div>
            <div class="mars spin"></div>
        </div>
    </div>
</body>

</html>
--}}