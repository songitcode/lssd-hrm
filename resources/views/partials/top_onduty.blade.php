<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 Onduty</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .od-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Main content với bố cục ngang */
        .od-main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .od-top-section {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .od-top-3-container {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .od-top-1-container {
            flex: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 149, 0, 0.05);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(255, 149, 0, 0.1);
            border: 2px solid rgba(255, 149, 0, 0.2);
        }

        /* Bố cục ngang cho top 4-10 */
        .od-horizontal-top-list {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 10px;
            margin-bottom: 20px;
            scroll-behavior: smooth;
        }

        .od-horizontal-top-list::-webkit-scrollbar {
            height: 8px;
        }

        .od-horizontal-top-list::-webkit-scrollbar-track {
            background: rgba(255, 149, 0, 0.1);
            border-radius: 10px;
        }

        .od-horizontal-top-list::-webkit-scrollbar-thumb {
            background: #ff9500;
            border-radius: 10px;
        }

        /* Card top 1 - lớn nhất */
        .od-top-1-card {
            width: 100%;
            max-width: 400px;
            background: linear-gradient(145deg, #ffffff, #fff5e6);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(255, 149, 0, 0.15);
            border: 2px solid rgba(255, 149, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .od-top-1-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ff9500, #ffaa33, #ff9500);
        }

        .od-top-1-rank {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background: #ff9500;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            box-shadow: 0 0 20px rgba(255, 149, 0, 0.4);
        }

        .od-top-1-medal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 3rem;
            color: #ff9500;
            text-shadow: 0 0 10px rgba(255, 149, 0, 0.3);
        }

        .od-top-1-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 20px auto;
            border: 4px solid #ff9500;
            overflow: hidden;
            background-color: #fff5e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: #ff9500;
        }

        .od-top-1-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .od-top-1-position {
            font-size: 1.2rem;
            color: #ff9500;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .od-top-1-career {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .od-top-1-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            background: rgba(255, 149, 0, 0.05);
            padding: 15px;
            border-radius: 10px;
        }

        .od-stat-item {
            text-align: center;
        }

        .od-stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff9500;
        }

        .od-stat-label {
            font-size: 0.85rem;
            color: #777;
            margin-top: 5px;
        }

        /* Card top 2-3 */
        .od-top-other-card {
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            border-radius: 15px;
            padding: 25px;
            flex: 1;
            min-width: 280px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-top: 4px solid #ff9500;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .od-top-other-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 149, 0, 0.15);
        }

        .od-top-other-rank {
            display: inline-block;
            width: 45px;
            height: 45px;
            background: #ff9500;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(255, 149, 0, 0.4);
        }

        .od-top-other-name {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .od-top-other-position {
            font-size: 1rem;
            color: #ff9500;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .od-top-other-career {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .od-top-other-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        /* Card top 4-10 */
        .od-horizontal-card {
            flex: 0 0 auto;
            width: 250px;
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-left: 4px solid #ff9500;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .od-horizontal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(255, 149, 0, 0.15);
        }

        .od-horizontal-rank {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #ff9500;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: bold;
            color: white;
            margin-bottom: 12px;
            box-shadow: 0 0 8px rgba(255, 149, 0, 0.3);
        }

        .od-horizontal-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .od-horizontal-position {
            font-size: 0.9rem;
            color: #ff9500;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .od-horizontal-career {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .od-horizontal-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }

        .od-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .od-stat-name {
            font-size: 0.8rem;
            color: #777;
        }

        .od-stat-value {
            font-size: 1rem;
            font-weight: bold;
            color: #ff9500;
        }

        .od-error-stat {
            color: #ff5555;
        }

        .od-good-stat {
            color: #22bb33;
        }

        /* Điều khiển */
        .od-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .od-control-btn {
            padding: 10px 20px;
            background: rgba(255, 149, 0, 0.1);
            color: #ff9500;
            border: 1px solid rgba(255, 149, 0, 0.3);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .od-control-btn:hover {
            background: rgba(255, 149, 0, 0.2);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 149, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .od-top-section {
                flex-direction: column;
            }

            .od-top-3-container {
                flex-direction: row;
                justify-content: space-between;
            }

            .od-top-other-card {
                min-width: 30%;
            }
        }

        @media (max-width: 768px) {
            .od-top-3-container {
                flex-direction: column;
            }

            .od-horizontal-card {
                width: 220px;
            }
        }

        @media (max-width: 480px) {
            .od-container {
                padding: 10px;
            }

            .od-horizontal-card {
                width: 200px;
            }

            .od-top-1-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    @if($ondutyRanking->isNotEmpty())
        <details>
            <summary class="od-control-btn m-4 text-center">XEM - TOP 10 ONDUTY TRONG 30-31 NGÀY GẦN NHẤT</summary>
            <div class="od-container">
                
                <main class="od-main-content">
                    <!-- Top 1 nổi bật -->
                    <div class="od-top-section">
                        <div class="od-top-1-container">
                            @php $top1 = $ondutyRanking[0] ?? null; @endphp
                            @if($top1 !== null)
                                <div class="od-top-1-card">
                                    <div class="od-top-1-rank">1</div>
                                    <div class="od-top-1-medal"><i class="fas fa-crown"></i></div>
                                    <div class="od-top-1-avatar">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div class="od-top-1-name">
                                        {{ strtoupper($top1['user']->employee->name_ingame) }}
                                    </div>

                                    <div class="od-top-1-position">
                                        {{ $top1['user']->employee->rank->name_ranks }} -
                                        {{ $top1['user']->employee->position->name_positions }}
                                    </div>

                                    <div class="od-top-1-career"><small>{{ $top1['completion_rate'] }}% Tỉ Lệ hoàn thành</small>
                                    </div>

                                    <div class="od-top-1-stats">
                                        <div class="od-stat-item">
                                            <div class="od-stat-value">{{ $top1['onduty_count'] }}</div>
                                            <div class="od-stat-label">Bảng Onduty</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value">{{ $top1['total_hours'] }}h</div>
                                            <div class="od-stat-label">Tổng giờ</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value od-error-stat">{{ $top1['errors'] }}</div>
                                            <div class="od-stat-label">Lỗi</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value od-good-stat">{{ number_format($top1['total_wage']) }}$
                                            </div>
                                            <div class="od-stat-label">Lương</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div>Không có dữ liệu Onduty.</div>
                            @endif
                        </div>

                        <!-- Top 2-3 -->
                        <div class="od-top-3-container">
                            @foreach($ondutyRanking->slice(1, 2) as $i => $row)
                                <div class="od-top-other-card">
                                    <div class="od-top-other-rank">{{ $i + 1 }}</div>

                                    <div class="od-top-other-name d-flex align-items-center">
                                        {{ strtoupper($row['user']->employee->name_ingame) }} -
                                        <div class="od-stat-value ms-2">Lương <span
                                                class="od-good-stat ">{{ number_format($row['total_wage']) }}$</span></div>
                                    </div>

                                    <div class="od-top-other-position">
                                        {{ $row['user']->employee->rank->name_ranks }} -
                                        {{ $row['user']->employee->position->name_positions }}
                                    </div>

                                    <div class="od-top-other-stats">
                                        <div class="od-stat-item">
                                            <div class="od-stat-value">{{ $row['onduty_count'] }}</div>
                                            <div class="od-stat-label">Bảng Onduty</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value">{{ $row['total_hours'] }}h</div>
                                            <div class="od-stat-label">Tổng giờ</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value od-error-stat">{{ $row['errors'] }}</div>
                                            <div class="od-stat-label">Lỗi</div>
                                        </div>
                                        <div class="od-stat-item">
                                            <div class="od-stat-value od-good-stat">
                                                {{ $row['completion_rate'] }}%
                                            </div>
                                            <div class="od-stat-label">Tỉ Lệ hoàn thành</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Top 4-10 - Bố cục ngang -->
                    <div class="od-horizontal-top-list">
                        @foreach($ondutyRanking->slice(3) as $i => $row)
                            <div class="od-horizontal-card">
                                <div class="od-horizontal-rank">{{ $i + 1 }}</div>

                                <div class="od-horizontal-name">
                                    {{ strtoupper($row['user']->employee->name_ingame) }}
                                </div>

                                <div class="od-horizontal-position">
                                    {{ $row['user']->employee->rank->name_ranks }} -
                                    {{ $row['user']->employee->position->name_positions }}
                                </div>

                                <div class="od-horizontal-stats">
                                    <div class="od-stat-row">
                                        <span>Bảng Onduty:</span> <strong>{{ $row['onduty_count'] }}</strong>
                                    </div>
                                    <div class="od-stat-row">
                                        <span>Tổng giờ:</span> <strong>{{ $row['total_hours'] }}h</strong>
                                    </div>
                                    <div class="od-stat-row">
                                        <span>Lỗi:</span>
                                        <strong class="od-error-stat">{{ $row['errors'] }}</strong>
                                    </div>
                                    <div class="od-stat-row">
                                        <span>Tỉ Lệ hoàn thành:</span>
                                        <strong class="od-good-stat">{{ $row['completion_rate'] }}%</strong>
                                    </div>
                                    <div class="od-stat-row">
                                        <span>Lương</span>
                                        <strong class="od-good-stat">{{ number_format($row['total_wage']) }}$</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Điều khiển -->
                    <div class="od-controls">
                        <button class="od-control-btn" id="od-scrollLeftBtn"><i class="fas fa-chevron-left"></i>
                            Trái</button>
                        <button class="od-control-btn" id="od-scrollRightBtn">Phải <i
                                class="fas fa-chevron-right"></i></button>
                    </div>
                </main>
            </div>
        </details>
    @else
        <div class="od-top-1-container mt-4">Không có dữ liệu Onduty.</div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Xử lý cuộn ngang cho danh sách top 4-10
            const scrollContainer = document.querySelector('.od-horizontal-top-list');
            const scrollLeftBtn = document.getElementById('od-scrollLeftBtn');
            const scrollRightBtn = document.getElementById('od-scrollRightBtn');

            scrollLeftBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: -250, behavior: 'smooth' });
            });

            scrollRightBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: 250, behavior: 'smooth' });
            });

            // Hiệu ứng xuất hiện cho các card
            const cards = document.querySelectorAll('.od-top-other-card, .od-horizontal-card');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Hiệu ứng cho top 1 card
            const top1Card = document.querySelector('.od-top-1-card');
            top1Card.style.opacity = '0';
            top1Card.style.transform = 'scale(0.9)';
            top1Card.style.transition = 'opacity 0.8s ease 0.2s, transform 0.8s ease 0.2s';

            setTimeout(() => {
                top1Card.style.opacity = '1';
                top1Card.style.transform = 'scale(1)';
            }, 300);
        });
    </script>
</body>

</html>