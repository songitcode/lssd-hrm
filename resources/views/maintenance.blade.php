<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảo trì | Hệ thống chấm công LSSD</title>
    <meta name="description" content="Trang bảo trì hệ thống chấm công LSSD GTA V RP">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0b1220;
            /* midnight */
            --bg-2: #0f1b35;
            /* deep navy */
            --accent: #ffd760;
            /* sheriff gold */
            --accent-2: #69c1ff;
            /* patrol blue */
            --text: #e6eefc;
            --muted: #a9b4c9;
            --chip: #1d2844;
            --success: #22c55e;
            --warn: #f59e0b;
            --danger: #ef4444;
            --card: rgba(20, 28, 51, 0.7);
            --border: rgba(255, 255, 255, 0.08);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
            --radius: 24px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
            color: var(--text);
            background:
                radial-gradient(1200px 600px at 120% 10%, rgba(105, 193, 255, 0.12), transparent 60%),
                radial-gradient(1000px 800px at -10% 110%, rgba(255, 215, 96, 0.12), transparent 60%),
                linear-gradient(160deg, var(--bg-1), var(--bg-2));
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(860px, 96vw);
            background: var(--card);
            backdrop-filter: blur(10px) saturate(130%);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            scale: .7;
        }

        .header {
            display: grid;
            grid-template-columns: 86px 1fr auto;
            gap: 20px;
            align-items: center;
            padding: 28px 28px 18px 28px;
            border-bottom: 1px dashed var(--border);
        }

        .badge {
            position: relative;
            width: 86px;
            height: 86px;
            border-radius: 24px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 215, 96, 0.25), rgba(255, 215, 96, 0.05));
            border: 1px solid rgba(255, 215, 96, 0.35);
            display: grid;
            place-items: center;
            box-shadow: inset 0 0 40px rgba(255, 215, 96, 0.08);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-4px)
            }
        }

        /* Simple shield + star */
        .badge svg {
            width: 58px;
            height: 58px;
        }

        .title h1 {
            margin: 0 0 6px 0;
            font-weight: 800;
            letter-spacing: 0.2px;
            font-size: clamp(22px, 3.2vw, 30px);
        }

        .title p {
            margin: 0;
            color: var(--muted);
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chip {
            font-weight: 600;
            font-size: 12.5px;
            letter-spacing: 0.4px;
            padding: 8px 10px;
            border-radius: 999px;
            background: var(--chip);
            border: 1px solid var(--border);
            text-transform: uppercase;
            opacity: 0.95;
        }

        .chip .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        .body {
            padding: 22px 28px 28px 28px;
            display: grid;
            gap: 18px;
        }

        .row {
            display: grid;
            gap: 12px;
        }

        @media (min-width: 780px) {
            .row.two {
                grid-template-columns: 1.1fr 0.9fr;
                gap: 18px;
            }
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px 16px 18px 16px;
            background: rgba(255, 255, 255, 0.015);
        }

        .panel h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--muted);
        }

        .panel p {
            margin: 4px 0;
        }

        .services {
            display: grid;
            gap: 8px;
        }

        .service {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .service .name {
            font-weight: 600;
        }

        .service .status {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #16203b;
            border: 1px solid var(--border);
        }

        .bar {
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            background: #101a31;
            border: 1px solid var(--border);
        }

        .bar .progress {
            height: 100%;
            width: 35%;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            animation: progress 2.8s ease-in-out infinite;
        }

        @keyframes progress {
            0% {
                width: 12%
            }

            50% {
                width: 68%
            }

            100% {
                width: 12%
            }
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            border: 1px solid var(--border);
            background: #111a33;
            transition: transform .12s ease, box-shadow .12s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .footer {
            padding: 16px 24px 24px 24px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        .footer code {
            background: rgba(255, 255, 255, 0.06);
            padding: 2px 6px;
            border-radius: 6px;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .eta {
            color: var(--accent);
            font-weight: 700;
        }

        /* Terminal effect */
        .terminal {
            background: #0a0f1c;
            color: #efb036;
            font-family: monospace;
            font-size: 14px;
            line-height: 1.4;
            padding: 12px;
            border-radius: 8px;
            height: 120px;
            overflow-y: auto;
            position: relative;
            scrollbar-width: none;
        }

        .terminal::after {
            content: "";
            position: absolute;
            bottom: 8px;
            left: 12px;
            width: 10px;
            height: 18px;
            background: #efb036;
            animation: blink 1s steps(2, start) infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <main class="card" role="main" aria-labelledby="title">
        <header class="header">
            <div class="badge" aria-hidden="true">
                <!-- Simple shield with star (LSSD vibe) -->
                <!-- <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
          <path d="M32 6c6 8 16 6 16 6s2 20-16 34C14 32 16 12 16 12s10 2 16-6Z" stroke="#ffd760" stroke-width="2" fill="rgba(255,215,96,0.06)"/>
          <path d="M32 22l2.4 6.6h7l-5.6 4.1 2.3 6.7L32 35.8l-6 3.6 2.3-6.7-5.6-4.1h7L32 22Z" fill="#ffd760"/>
        </svg> -->
                <img src="{{ asset('assets/images/Logo_LSCSD.png') }}"
                    alt="" width="64" height="64">
            </div>

            <div class="title">
                <h1 id="title">Hệ thống chấm công LSSD đang bảo trì</h1>
                <p>Chúng tôi đang nâng cấp để hệ thống ổn định hơn, hiển thị lương chính xác và đồng bộ tốt hơn.</p>
            </div>

            <div class="chips" aria-label="Trạng thái">
                <span class="chip"><span class="dot" style="background: var(--warn)"></span> Bảo trì định kỳ</span>
                <span class="chip"><span class="dot" style="background: var(--accent)"></span> Ưu tiên cao</span>
            </div>
        </header>

        <section class="body">
            <div class="row two">
                <div class="panel">
                    <h3>Tiến độ</h3>
                    <div class="bar" aria-hidden="true">
                        <div class="progress"></div>
                    </div>
                    <p>Thời gian dự kiến khôi phục: <span class="eta">—</span></p>
                    <p>Mã bảo trì: <code>LSSD-MNT-2025-08</code></p>
                    <div class="terminal" id="terminal"></div>
                </div>

                <div class="panel">
                    <h3>Liên hệ hỗ trợ</h3>
                    <div class="links" role="group" aria-label="Liên hệ">
                        <a class="btn" href="#" aria-label="Mở Discord LSSD">🛡️ Discord LSSD</a>
                        <a class="btn" href="tel:+84854112509" aria-label="Gọi hotline">📞 Hotline</a>
                        <a class="btn" href="tel:+84854112509" aria-label="Gọi hotline">
                            <img src="{{ asset('assets/images/Logo_LSCSD.png') }}"
                                alt="" width="30" height="30">Cấp Quản Lý LSSD</a>
                        <a class="btn" href="https://jebsoon.netlify.app/" aria-label="Mở Link" target="_blank">
                            🌐Jebb</a>
                        <p class="btn">
                            <img src="https://media.discordapp.net/attachments/1298536044907462736/1401817389259034766/IMG_20250703_102159.png?ex=68a56e2e&is=68a41cae&hm=c4d917b73b501616ea24390fc1081e2b2d3cde5d395a654d3e36d9a71e26624a&=&format=webp&quality=lossless&width=841&height=929"
                                alt="" width="30" height="30" style="border-radius: 50%;"> Son Myname
                        </p>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h3>Dịch vụ bị ảnh hưởng</h3>
                <div class="services" role="list">
                    <div class="service" role="listitem">
                        <span class="name">Chấm công theo ca</span>
                        <span class="status">Tạm dừng</span>
                    </div>
                    <div class="service" role="listitem">
                        <span class="name">Bảng lương tháng</span>
                        <span class="status">Tạm dừng</span>
                    </div>
                    <div class="service" role="listitem">
                        <span class="name">Hỗ trợ xử án</span>
                        <span class="status">Gián đoạn</span>
                    </div>
                    <div class="service" role="listitem">
                        <span class="name">Hỗ trợ truy nã</span>
                        <span class="status">Gián đoạn</span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h3>Ghi chú</h3>
                <p>Trong thời gian bảo trì, mọi dữ liệu chấm công đã ghi nhận <strong>được giữ an toàn</strong> và không
                    bị ảnh
                    hưởng. Bạn có thể quay lại sau để tiếp tục thao tác.</p>
            </div>
        </section>

        <div class="footer">
            Máy chủ có thể phản hồi <code>503 Service Unavailable</code> trong thời gian bảo trì.
        </div>
    </main>
    <script>
        const terminal = document.getElementById('terminal');
        const lines = [
            "[INFO] Bắt đầu quá trình bảo trì hệ thống...",
            "[OK] Đã backup toàn bộ dữ liệu chấm công.",
            "[INFO] Đang cập nhật cơ sở dữ liệu...",
            "[INFO] Tiến hành kiểm tra tính năng mới...",
            "[INFO] Kiểm tra lần 1...",
            "[INFO] Kiểm tra lần 2...",
            "[INFO] Kiểm tra lần 3...",
            "[INFO] Kiểm tra lần 4..N...",
            "[OK] Kiểm tra hoàn tất...",
            "[INFO] 50%=====>...",
            "[INFO] 60%=========>...",
            "[INFO] 70%===========>...",
            "[INFO] 80%=============>...",
            "[INFO] 90%===============>...",
            "[INFO] 100%===================>",
            "[OK] Hoàn tất quá trình loading",
            "[WARN] Một số dịch vụ tạm thời gián đoạn.",
            "[OK] Tiến trình sắp hoàn tất."
        ];

        let i = 0;
        function printLine() {
            if (i < lines.length) {
                terminal.innerHTML += `> ${lines[i]}<br>`;
                terminal.scrollTop = terminal.scrollHeight;
                i++;
                setTimeout(printLine, 1300);
            } else {
                i = 0;
                terminal.innerHTML = "";
                setTimeout(printLine, 1000);
            }
        }
        printLine();
    </script>
</body>

</html>