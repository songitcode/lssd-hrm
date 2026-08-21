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
        .maintenance-wrapper {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-top: 4px solid var(--gold);
            border-radius: 18px;
            padding: 70px 40px;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .maintenance-icon {
            font-size: 90px;
            color: var(--gold);
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }

        .maintenance-title {
            font-family: 'Oswald';
            font-size: 38px;
            letter-spacing: 4px;
            color: var(--gold);
        }

        .maintenance-sub {
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 17px;
            max-width: 700px;
            margin-inline: auto;
            line-height: 1.8;
        }

        .maintenance-info {
            margin-top: 35px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .info-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="maintenance-wrapper m-5 container">

        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>

        <div class="maintenance-title">
            HỆ THỐNG ĐANG BẢO TRÌ
        </div>

        <div class="maintenance-sub">
            Bảo trì rồi lấy gì nhận lương đêyy 😁
        </div>

        <div class="maintenance-info">

            <div class="info-card">
                <i class="fas fa-clock"></i>
                <h5>Thời gian</h5>
                <p>Dự kiến hoàn thành<br>Theo dõi thông báo Discord</p>
            </div>

            <div class="info-card">
                <i class="fas fa-shield-alt"></i>
                <h5>Dữ liệu</h5>
                <p>Mọi dữ liệu đều được bảo toàn.</p>
            </div>

            <div class="info-card">
                <i class="fab fa-discord"></i>
                <h5>Thông báo</h5>
                <p>Theo dõi Discord để biết thời gian mở lại.</p>
            </div>

        </div>

    </div>


</body>

</html>