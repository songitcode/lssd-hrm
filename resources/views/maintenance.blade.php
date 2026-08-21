<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảo trì hệ thống | LSSD Attendance Management System</title>
    <meta name="description" content="Hệ thống chấm công LSSD đang được bảo trì nâng cấp để phục vụ tốt hơn">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.7-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-1: #0a0f1a;
            --bg-2: #0e1527;
            --bg-3: #121c35;
            --accent: #ffd44d;
            --accent-glow: rgba(255, 212, 77, 0.15);
            --accent-2: #4d9fff;
            --text: #f0f4ff;
            --text-muted: #a1b0d3;
            --text-light: #c7d1ed;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --border: rgba(255, 255, 255, 0.08);
            --border-light: rgba(255, 255, 255, 0.12);
            --card-bg: rgba(18, 28, 53, 0.7);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
            background:
                radial-gradient(ellipse at 80% 10%, rgba(105, 193, 255, .08), transparent 50%),
                radial-gradient(ellipse at 20% 90%, rgba(255, 215, 96, .08), transparent 50%),
                linear-gradient(180deg, var(--bg-1) 0%, var(--bg-2) 100%);
            min-height: 100vh;
            padding: 40px 20px;
            overflow-x: hidden;
            overflow-y: auto;
            display: block;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(77, 159, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 212, 77, 0.03) 0%, transparent 50%);
            z-index: -1;
        }

        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
            scale: 0.5;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 40px auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-card {
            min-height: auto;
            overflow: visible;
            background: var(--card-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            z-index: 10;
        }

        .header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 30px 35px 25px;
            border-bottom: 1px solid var(--border);
            gap: 20px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .badge-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .badge {
            width: 100%;
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(255, 212, 77, 0.1), rgba(77, 159, 255, 0.1));
            border: 1px solid rgba(255, 212, 77, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(255, 212, 77, 0.2);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(255, 212, 77, 0);
            }
        }

        .badge::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transform: rotate(45deg);
            animation: shine 8s infinite linear;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .badge img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            z-index: 1;
        }

        .title-section h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }

        .title-section p {
            color: var(--text-muted);
            font-size: 15px;
            max-width: 500px;
            line-height: 1.5;
        }

        .status-indicators {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-light);
        }

        .status-tag .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            position: relative;
        }

        .status-tag .dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            animation: pulseDot 2s infinite;
        }

        .status-tag.maintenance .dot {
            background-color: var(--warning);
        }

        .status-tag.maintenance .dot::after {
            background-color: var(--warning);
        }

        .status-tag.priority .dot {
            background-color: var(--accent);
        }

        .status-tag.priority .dot::after {
            background-color: var(--accent);
        }

        @keyframes pulseDot {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            70% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .content {
            display: grid;
            grid-template-columns: 1.3fr .7fr;
            gap: 25px;
            align-items: start;
        }

        @media (min-width: 992px) {
            .content {
                grid-template-columns: 1.2fr 0.8fr;
            }
        }

        .progress-section {
            grid-column: 1;
        }

        .progress-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 24px;
            height: 100%;
        }

        .progress-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-card h3 i {
            font-size: 18px;
        }

        .progress-info {
            margin-bottom: 25px;
        }

        .progress-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .progress-label {
            color: var(--text-muted);
            font-size: 14px;
        }

        .progress-value {
            font-weight: 700;
            color: var(--accent);
            font-size: 14px;
        }

        .progress-bar {
            height: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            border-radius: 10px;
            position: relative;
            animation: progressAnimation 3s ease-in-out infinite alternate;
        }

        @keyframes progressAnimation {
            0% {
                width: 65%;
            }

            100% {
                width: 70%;
            }
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .time-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .time-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 15px;
        }

        .time-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .time-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .maintenance-code {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .code-label {
            font-size: 14px;
            color: var(--text-muted);
        }

        .code-value {
            font-family: monospace;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 1px;
        }

        .contact-section {
            grid-column: 2;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 24px;
            height: 100%;
        }

        .contact-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--accent-2);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-card h3 i {
            font-size: 18px;
        }

        .contact-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--accent-glow);
            transform: translateY(-2px);
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: rgba(255, 212, 77, 0.1);
            color: var(--accent);
            flex-shrink: 0;
        }

        .contact-info {
            flex-grow: 1;
        }

        .contact-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text);
        }

        .contact-desc {
            font-size: 13px;
            color: var(--text-muted);
        }

        .services-section {
            grid-column: 1 / -1;
        }

        .services-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 24px;
        }

        .services-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .services-card h3 i {
            font-size: 18px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .service-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .service-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .service-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .service-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            color: var(--accent-2);
            font-size: 16px;
        }

        .service-name {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-light);
            width: 100%;
        }

        .service-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-status.paused {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
            width: 100px;
        }

        .service-status.disrupted {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .notes-section {
            grid-column: 1 / -1;
        }

        .notes-card {
            background: rgba(255, 212, 77, 0.05);
            border: 1px solid rgba(255, 212, 77, 0.15);
            border-radius: var(--radius-md);
            padding: 24px;
        }

        .notes-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notes-card h3 i {
            font-size: 18px;
        }

        .notes-content {
            color: var(--text-light);
            line-height: 1.6;
        }

        .notes-content strong {
            color: var(--accent);
        }

        .footer {
            padding: 25px 35px;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        .footer code {
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid var(--border);
            color: var(--accent);
            font-family: monospace;
            font-size: 13px;
            margin: 0 4px;
        }

        .terminal {
            scrollbar-width: none;
        }

        .terminal-container {
            margin-top: 25px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .terminal-header {
            background: rgba(0, 0, 0, 0.3);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .terminal-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .terminal-controls {
            display: flex;
            gap: 8px;
        }

        .terminal-control {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .terminal-control.close {
            background: #ff5f56;
        }

        .terminal-control.minimize {
            background: #ffbd2e;
        }

        .terminal-control.maximize {
            background: #27c93f;
        }

        .terminal {
            background: rgba(10, 15, 26, 0.8);
            color: #4dffb8;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            padding: 20px;
            height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        .terminal-line {
            margin-bottom: 4px;
        }

        .terminal-line::before {
            content: '> ';
            color: var(--accent);
        }

        .blinking-cursor {
            display: inline-block;
            width: 8px;
            height: 16px;
            background-color: #4dffb8;
            animation: blink 1s infinite;
            vertical-align: middle;
            margin-left: 4px;
        }

        @keyframes blink {

            0%,
            50% {
                opacity: 1;
            }

            51%,
            100% {
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .status-indicators {
                width: 100%;
            }

            .content {
                grid-template-columns: 1fr;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .time-info {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:992px) {

            body {
                display: block;
                padding: 20px 15px;
            }

            .container {
                margin: 0 auto;
            }

            .content {
                grid-template-columns: 1fr;
            }

            .progress-section,
            .contact-section,
            .services-section,
            .notes-section {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>
    <div class="particles" id="particles"></div>

    <div class="container">
        <div class="maintenance-card">
            <div class="header">
                <div class="logo-section">
                    <div class="badge-container">
                        <div class="badge">
                            <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="LSSD Badge">
                        </div>
                    </div>
                    <div class="title-section">
                        <h1>HỆ THỐNG CHẤM CÔNG LSSD ĐANG BẢO TRÌ</h1>
                        <p>Chúng tôi đang nâng cấp hệ thống để cải thiện hiệu suất, độ ổn định và độ chính xác của dữ
                            liệu lương. Mọi dữ liệu đã được sao lưu an toàn.</p>
                    </div>
                </div>

                <div class="status-indicators">
                    <div class="status-tag maintenance">
                        <span class="dot"></span>
                        <span>Bảo trì định kỳ</span>
                    </div>
                    <div class="status-tag priority">
                        <span class="dot"></span>
                        <span>Ưu tiên cao</span>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="progress-section">
                    <div class="progress-card">
                        <h3><i class="fas fa-tasks"></i> Tiến độ bảo trì</h3>

                        <div class="progress-info">
                            <div class="progress-details">
                                <span class="progress-label">Tiến độ tổng thể</span>
                                <span class="progress-value" id="progress-value">65%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progress-fill"></div>
                            </div>
                        </div>

                        <div class="time-info">
                            <div class="time-item">
                                <div class="time-label"><i class="far fa-clock"></i> Bắt đầu</div>
                                <div class="time-value" id="start-time">10:00, 15/08/2024</div>
                            </div>
                            <div class="time-item">
                                <div class="time-label"><i class="far fa-hourglass"></i> Dự kiến hoàn thành</div>
                                <div class="time-value" id="end-time">Ai biết đâu à 🤫, 15/08/2024</div>
                            </div>
                            <div class="time-item">
                                <div class="time-label"><i class="fas fa-history"></i> Còn lại</div>
                                <div class="time-value" id="remaining-time">~ Ai biết đâu à 🤫</div>
                            </div>
                        </div>

                        <div class="maintenance-code">
                            <span class="code-label">Mã bảo trì:</span>
                            <span class="code-value">LSSD-MNT202608001</span>
                        </div>

                        <div class="terminal-container">
                            <div class="terminal-header">
                                <div class="terminal-title">
                                    <i class="fas fa-terminal"></i> Live Maintenance Log
                                </div>
                                <div class="terminal-controls">
                                    <div class="terminal-control close"></div>
                                    <div class="terminal-control minimize"></div>
                                    <div class="terminal-control maximize"></div>
                                </div>
                            </div>
                            <div class="terminal" id="terminal">
                                <!-- Terminal content will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-section">
                    <div class="contact-card">
                        <h3><i class="fas fa-globe"></i> Hỗ Trợ Xử Án/ Lập Án</h3>

                        <a href="https://jaysonmanager2.github.io/lssd-form/" target="_blank" class="contact-item mb-3">
                            <div class="contact-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">LSSD.COM</div>
                                <div class="contact-desc">Kênh hỗ trợ nhanh nhất</div>
                            </div>
                        </a>
                        <h3><i class="fas fa-headset"></i> Liên hệ hỗ trợ</h3>

                        <div class="contact-list">
                            <a href="https://discord.gg/lssd" target="_blank" class="contact-item">
                                <div class="contact-icon">
                                    <i class="fab fa-discord"></i>
                                </div>
                                <div class="contact-info">
                                    <div class="contact-name">Discord LSSD</div>
                                    <div class="contact-desc">Kênh hỗ trợ nhanh nhất</div>
                                </div>
                            </a>

                            {{--<a href="tel:+84854112509" class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                 <div class="contact-info">
                                    <div class="contact-name">Hotline khẩn cấp</div>
                                    <div class="contact-desc">+84 85 411 2509</div>
                                </div>
                            </a>--}}

                            <a href="#" class="contact-item">
                                <div class="contact-icon">
                                    <img src="{{ asset('assets/images/Logo_LSCSD.png') }}" alt="LSSD"
                                        style="width: 24px; height: 24px;">
                                </div>
                                <div class="contact-info">
                                    <div class="contact-name">Cấp Quản Lý LSSD</div>
                                    <div class="contact-desc">Liên hệ trực tiếp</div>
                                </div>
                            </a>

                            <a href="https://jebsoon.netlify.app/" target="_blank" class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="contact-info">
                                    <div class="contact-name">Trang chủ Jebb</div>
                                    <div class="contact-desc">jebsoon.netlify.app</div>
                                </div>
                            </a>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <img src="https://cdn.discordapp.com/avatars/440837500848570376/977636aa0e1055fb32035c4c9c18c5e7.webp?size=128"
                                        alt="hiimson" style="width: 24px; height: 24px; border-radius: 50%;">
                                </div>
                                <div class="contact-info">
                                    <div class="contact-name">hiimson (Son Myname)</div>
                                    <div class="contact-desc">Quản trị viên hệ thống</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="services-section">
                    <div class="services-card">
                        <h3><i class="fas fa-server"></i> Dịch vụ bị ảnh hưởng</h3>

                        <div class="services-grid">
                            <div class="service-item">
                                <div class="service-info">
                                    <div class="service-icon">
                                        <i class="far fa-calendar-check"></i>
                                    </div>
                                    <span class="service-name">Chấm công</span>
                                </div>
                                <span class="service-status paused">Tạm dừng</span>
                            </div>

                            <div class="service-item">
                                <div class="service-info">
                                    <div class="service-icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <span class="service-name">Bảng lương</span>
                                </div>
                                <span class="service-status paused">Tạm dừng</span>
                            </div>

                            <div class="service-item">
                                <div class="service-info">
                                    <div class="service-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <span class="service-name">QL nhân sự</span>
                                </div>
                                <span class="service-status paused">Tạm dừng</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="notes-section">
                <div class="notes-card">
                    <h3><i class="fas fa-exclamation-circle"></i> Thông tin quan trọng</h3>
                    <div class="notes-content">
                        <p>Tất cả dữ liệu chấm công và lương <strong>đã được sao lưu an toàn</strong> trước khi bảo
                            trì. Hệ thống sẽ tự động cập nhật và đồng bộ dữ liệu sau khi hoàn tất nâng cấp.</p>
                        <p style="margin-top: 10px;">Trong thời gian bảo trì, các chức năng liên quan đến chấm công
                            và tính lương sẽ tạm ngưng. Bạn vẫn có thể truy cập các thông tin tĩnh và liên hệ hỗ trợ
                            qua các kênh bên trên.</p>
                        <p style="margin-top: 10px;"><strong>Lưu ý:</strong> Máy chủ có thể trả về mã lỗi
                            <code>503 Service Unavailable</code> trong suốt quá trình bảo trì.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>© 2026 Los Santos Sheriff's Department | Hệ thống quản lý chấm công & nhân sự</p>
            <p style="margin-top: 8px; font-size: 13px;">Mọi thắc mắc xin liên hệ qua Discord LSSD </p>
        </div>
    </div>
    <script>
        // Create particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 4 + 1;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const opacity = Math.random() * 0.1 + 0.02;
                const duration = Math.random() * 20 + 10;
                const delay = Math.random() * 5;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.opacity = opacity;
                particle.style.animation = `float ${duration}s ease-in-out ${delay}s infinite`;

                particlesContainer.appendChild(particle);
            }
        }

        // Simulate progress
        function simulateProgress() {
            const progressValue = document.getElementById('progress-value');
            const progressFill = document.getElementById('progress-fill');

            let progress = 65;
            const targetProgress = 100;

            const interval = setInterval(() => {
                progress += Math.random() * 0.5;

                if (progress >= targetProgress) {
                    progress = targetProgress;
                    clearInterval(interval);

                    // When complete, show completion message
                    setTimeout(() => {
                        progressValue.textContent = "100%";
                        progressFill.style.width = "100%";
                        progressFill.style.background = "linear-gradient(90deg, #22c55e, #4ade80)";
                    }, 100);
                } else {
                    progressValue.textContent = `${Math.floor(progress)}%`;
                    progressFill.style.width = `${progress}%`;
                }
            }, 2000);

            return interval;
        }

        // Terminal simulation
        function simulateTerminal() {
            const terminal = document.getElementById('terminal');

            const lines = [
                `[LSSD_CALL] INFO: Bắt đầu quá trình bảo trì hệ thống...`,
                `[LSSD_CALL] INFO: Đang ngắt kết nối người dùng...`,
                `[LSSD_CALL] OK: Tất cả phiên làm việc đã được lưu và đóng.`,
                `[LSSD_CALL] INFO: Khởi tạo sao lưu cơ sở dữ liệu...`,
                `[LSSD_CALL] OK: Sao lưu hoàn tất. Kích thước: 2.4GB`,
                `[LSSD_CALL] INFO: Áp dụng bản cập nhật cơ sở dữ liệu...`,
                `[LSSD_CALL] INFO: Đang cập nhật module chấm công (vx.x.x)...`,
                `[LSSD_CALL] INFO: Đang cập nhật module tính lương (vx.x.x)...`,
                `[LSSD_CALL] INFO: Kiểm tra tính tương thích các module...`,
                `[LSSD_CALL] OK: Tất cả module đã tương thích.`,
                `[LSSD_CALL] INFO: Đang tối ưu hóa hiệu suất hệ thống...`,
                `[LSSD_CALL] INFO: Tối ưu hóa chỉ mục cơ sở dữ liệu...`,
                `[LSSD_CALL] INFO: Chạy kiểm tra bảo mật...`,
                `[LSSD_CALL] OK: Không tìm thấy lỗ hổng bảo mật.`,
                `[LSSD_CALL] INFO: Khởi động lại các dịch vụ nền...`,
                `[LSSD_CALL] INFO: Khởi tạo kết nối với hệ thống LSPD...`,
                `[LSSD_CALL] OK: Đồng bộ hóa thành công với LSPD Database.`,
                `[LSSD_CALL] INFO: Chạy kiểm tra toàn diện...`,
                `[LSSD_CALL] INFO: Kiểm tra 1/5: Module chấm công...`,
                `[LSSD_CALL] OK: Module chấm công hoạt động bình thường.`,
                `[LSSD_CALL] INFO: Kiểm tra 2/5: Module tính lương...`,
                `[LSSD_CALL] OK: Module tính lương hoạt động bình thường.`,
                `[LSSD_CALL] INFO: Kiểm tra 3/5: Module báo cáo...`,
                `[LSSD_CALL] OK: Module báo cáo hoạt động bình thường.`,
                `[LSSD_CALL] INFO: Kiểm tra 4/5: API Integration...`,
                `[LSSD_CALL] OK: Tất cả API endpoints đang phản hồi.`,
                `[LSSD_CALL] INFO: Kiểm tra 5/5: Hiệu suất tổng thể...`,
                `[LSSD_CALL] OK: Hiệu suất được cải thiện 42%.`,
                `[LSSD_CALL] INFO: Chuẩn bị khôi phục dịch vụ...`,
            ];

            let currentLine = 0;

            function addLine() {
                if (currentLine < lines.length) {
                    const lineElement = document.createElement('div');
                    lineElement.classList.add('terminal-line');
                    lineElement.textContent = lines[currentLine];

                    terminal.appendChild(lineElement);
                    terminal.scrollTop = terminal.scrollHeight;

                    currentLine++;

                    // Random delay between lines
                    const delay = Math.random() * 1000 + 500;
                    setTimeout(addLine, delay);
                } else {
                    // Add blinking cursor at the end
                    const cursor = document.createElement('span');
                    cursor.classList.add('blinking-cursor');
                    terminal.appendChild(cursor);

                    // Restart after a while
                    setTimeout(() => {
                        terminal.innerHTML = '';
                        currentLine = 0;
                        setTimeout(addLine, 1000);
                    }, 10000);
                }
            }

            // Start the terminal simulation
            setTimeout(addLine, 1000);
        }

        // Update time information
        function updateTimeInfo() {
            const now = new Date();
            const startTime = new Date(now);
            startTime.setHours(10, 0, 0);

            const endTime = new Date(startTime);
            endTime.setHours(endTime.getHours() + 8);

            // Format dates
            const formatTime = (date) => {
                return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) +
                    ', ' + date.toLocaleDateString('vi-VN');
            };

            // Calculate remaining time
            const diffMs = endTime - now;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            const diffSeconds = Math.floor((diffMs % (1000 * 60)) / 1000);

            // Update DOM
            document.getElementById('start-time').textContent = formatTime(startTime);
            document.getElementById('end-time').textContent = formatTime(endTime);

            if (diffMs > 0) {
                document.getElementById('remaining-time').textContent = `~ ${diffHours} giờ ${diffMinutes} phút ${diffSeconds} giây`;
            } else {
                document.getElementById('remaining-time').textContent = "Đang hoàn tất...";
            }
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function () {
            createParticles();
            updateTimeInfo();
            simulateTerminal();
            const progressInterval = simulateProgress();

            // Update time every minute
            setInterval(updateTimeInfo, 1000);

            // Add hover effects to service items
            const serviceItems = document.querySelectorAll('.service-item');
            serviceItems.forEach(item => {
                item.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.2)';
                });

                item.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });

            // Add click effects to contact items
            const contactItems = document.querySelectorAll('.contact-item');
            contactItems.forEach(item => {
                item.addEventListener('mousedown', function () {
                    this.style.transform = 'translateY(1px)';
                });

                item.addEventListener('mouseup', function () {
                    this.style.transform = 'translateY(-2px)';
                });

                item.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>

</html>