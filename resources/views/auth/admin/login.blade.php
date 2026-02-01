@extends('layouts.app') {{-- Kế thừa layout --}}

@section('title', 'Admin Login')

@push('styles')
    <style>
        :root {
            --hacker-green: #00ff41;
            --matrix-green: #00cc33;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --terminal-text: #00ff9d;
            --glow-blue: #0066ff;
            --glow-purple: #9d00ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', monospace;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--hacker-green);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Hiệu ứng background matrix */
        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #0a0a0a 0%, #000000 100%);
            z-index: -2;
            overflow: hidden;
        }

        .matrix-code {
            position: absolute;
            color: rgba(0, 255, 65, 0.3);
            font-size: 14px;
            white-space: nowrap;
            animation: fall linear infinite;
            user-select: none;
        }

        @keyframes fall {
            from {
                transform: translateY(-100px);
            }

            to {
                transform: translateY(100vh);
            }
        }

        /* Container chính */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* Card đăng nhập */
        .login-card {
            background-color: rgba(10, 10, 10, 0.9);
            border: 1px solid var(--hacker-green);
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3),
                0 0 40px rgba(0, 102, 255, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg,
                    var(--hacker-green),
                    var(--glow-blue),
                    var(--glow-purple),
                    var(--hacker-green));
            z-index: -1;
            border-radius: 7px;
            animation: borderGlow 4s linear infinite;
            background-size: 400%;
        }

        @keyframes borderGlow {
            0% {
                background-position: 0%;
            }

            100% {
                background-position: 400%;
            }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .login-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: var(--terminal-text);
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.7);
        }

        .login-header p {
            color: #aaa;
            font-size: 0.9rem;
        }

        .hacker-icon {
            font-size: 2.5rem;
            color: var(--hacker-green);
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                text-shadow: 0 0 5px var(--hacker-green);
            }

            50% {
                text-shadow: 0 0 20px var(--hacker-green), 0 0 30px var(--hacker-green);
            }
        }

        /* Form */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            color: var(--matrix-green);
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 1px;
            font-size: 0.95rem;
        }

        .form-control {
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid #333;
            border-radius: 3px;
            color: var(--hacker-green);
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: rgba(30, 30, 30, 0.9);
            border-color: var(--hacker-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
            color: var(--terminal-text);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: var(--hacker-green);
        }

        /* Nút đăng nhập */
        .btn-login {
            background-color: transparent;
            border: 1px solid var(--hacker-green);
            color: var(--hacker-green);
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-radius: 3px;
            margin-top: 10px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background-color: rgba(0, 255, 65, 0.1);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
            color: white;
        }

        .btn-login:active {
            transform: translateY(2px);
        }

        /* Footer */
        .login-footer {
            margin-top: 25px;
            text-align: center;
            border-top: 1px solid #222;
            padding-top: 20px;
        }

        .login-footer p {
            color: #777;
            font-size: 0.85rem;
        }

        .login-footer a {
            color: var(--matrix-green);
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: var(--hacker-green);
            text-decoration: underline;
        }

        /* Terminal effect */
        .terminal-line {
            font-family: 'Courier New', monospace;
            color: var(--terminal-text);
            font-size: 0.9rem;
            margin-bottom: 5px;
            opacity: 0;
            animation: typeIn 0.5s forwards;
        }

        .terminal-line:nth-child(1) {
            animation-delay: 0.2s;
        }

        .terminal-line:nth-child(2) {
            animation-delay: 0.8s;
        }

        .terminal-line:nth-child(3) {
            animation-delay: 1.4s;
        }

        .terminal-line:nth-child(4) {
            animation-delay: 2.0s;
        }

        @keyframes typeIn {
            to {
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        /* Hiệu ứng scanning */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right,
                    transparent,
                    var(--hacker-green),
                    transparent);
            animation: scan 3s linear infinite;
            z-index: 2;
        }

        @keyframes scan {
            0% {
                top: 0%;
            }

            100% {
                top: 100%;
            }
        }

        /* Security level indicator */
        .security-level {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding: 10px;
            background-color: rgba(20, 20, 20, 0.7);
            border-radius: 3px;
            border-left: 3px solid var(--hacker-green);
        }

        .security-level i {
            margin-right: 10px;
            color: var(--hacker-green);
        }

        .security-level span {
            font-size: 0.9rem;
            color: #aaa;
        }
    </style>
@endpush
@section('hide_css')
@endsection
@section('hide_navbar')
@endsection

@section('content')
    <!-- Background matrix effect -->
    <div class="matrix-bg" id="matrixBg"></div>

    <!-- Scanning line -->
    <div class="scan-line"></div>

    <!-- Login container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Login header -->
            <div class="login-header">
                <div class="hacker-icon">
                    <i class="fas fa-user-secret"></i>
                </div>
                <h1>Admin Access</h1>
                <p>Secure Terminal v0.1.1</p>
                {{--
                <div class="mt-4">
                    <div class="terminal-line">> Kiểm tra kết nối...</div>
                    <div class="terminal-line">> Kiểm tra bảo mật...</div>
                    <div class="terminal-line">> Encryption: AES-256 enabled</div>
                    <div class="terminal-line">> Đợi...</div>
                </div>
                --}}
            </div>

            <!-- Login form -->
            <form id="loginForm" action="{{ route('admin.login.submit') }}" method="POST" class="form-login">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">USERNAME</label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter admin username"
                        required>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">PASSWORD</label>
                    <input type="password" class="form-control" name="password" id="password"
                        placeholder="Enter encryption key" required>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                </div>

                @if($errors->has('login'))
                    <div class="security-level mb-2">
                        <i class="fas fa-shield-alt"></i>
                        <span>{{ $errors->first('login') }}</span>
                    </div>
                @endif

                <button type="submit" class="btn-login">
                    <i class="fas fa-terminal me-2"></i> Access System
                </button>
            </form>

            <!-- Login footer -->
            <div class="login-footer">
                <p><i class="fas fa-exclamation-triangle me-1"></i> Unauthorized access is prohibited and monitored</p>
            </div>
        </div>
    </div>
@endsection
@section('hide_footer')
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const matrixBg = document.getElementById('matrixBg');
            const chars = "01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン";

            for (let i = 0; i < 80; i++) {
                const code = document.createElement('div');
                code.classList.add('matrix-code');

                // Random nội dung
                let content = '';
                const length = Math.floor(Math.random() * 15) + 10;
                for (let j = 0; j < length; j++) {
                    content += chars.charAt(Math.floor(Math.random() * chars.length));
                }

                code.textContent = content;
                code.style.left = Math.random() * 100 + '%';
                code.style.animationDelay = Math.random() * 5 + 's';
                code.style.animationDuration = (Math.random() * 10 + 10) + 's';

                matrixBg.appendChild(code);
            }

            // Xử lý form đăng nhập
            const loginForm = document.getElementById('loginForm');
            const btn = document.querySelector('.btn-login');
            const originalText = btn.innerHTML;

            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Authenticating...';
                btn.disabled = true;

                setTimeout(() => {
                    fetch('/admin/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({ username, password })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                btn.innerHTML = '<i class="fas fa-check me-2"></i> Access Granted';
                                btn.classList.add('btn-success');

                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1200);
                            } else {
                                showError(data.message);
                            }
                        })
                        .catch(() => {
                            showError('Server error. Please try again.');
                        });
                }, 1200);
            });

            function showError(message) {
                btn.innerHTML = '<i class="fas fa-times me-2"></i> Access Denied';
                btn.disabled = false;

                const errorMsg = document.createElement('div');
                errorMsg.className = 'security-level mt-3';
                errorMsg.innerHTML = `<i class="fas fa-shield-alt me-2"></i> ${message}`;

                loginForm.appendChild(errorMsg);

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    errorMsg.remove();
                }, 7000);
            }

            // Hiệu ứng nhấp nháy cho placeholder
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.setAttribute('placeholder', '');
                });

                input.addEventListener('blur', function () {
                    if (this.id === 'username') {
                        this.setAttribute('placeholder', 'Enter admin username');
                    } else {
                        this.setAttribute('placeholder', 'Enter encryption key');
                    }
                });
            });
        });

    </script>
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
@endpush