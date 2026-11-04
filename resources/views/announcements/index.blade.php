@extends('layouts.app')

@section('title', 'Thông báo')

@push('styles')
    <style>
        .announcement-hub {
            --ah-ink: #172033;
            --ah-muted: #718096;
            --ah-gold: #b88928;
            --ah-line: #e7ebf1;
            background: #f4f6fa;
            min-height: 75vh;
            padding: 2rem 0 4rem;
        }
        .ah-shell { max-width: 1180px; }
        .ah-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: clamp(1.7rem, 5vw, 3.6rem);
            margin-bottom: 1.5rem;
            color: #fff;
            background: radial-gradient(circle at 90% 15%, rgba(245,197,77,.23), transparent 28%),
                        linear-gradient(125deg, #182338, #263d5a 65%, #315271);
            box-shadow: 0 20px 50px rgba(24,35,56,.15);
        }
        .ah-hero:after {
            content: "★";
            position: absolute;
            right: 4%;
            bottom: -95px;
            color: rgba(255,255,255,.055);
            font-size: 290px;
            line-height: 1;
            pointer-events: none;
        }
        .ah-kicker { color: #f5c54d; font-size: .72rem; font-weight: 800; letter-spacing: .19em; }
        .ah-hero h1 { max-width: 700px; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; letter-spacing: -.045em; }
        .ah-hero p { max-width: 620px; color: #d3deec; }
        .ah-hero .btn { position: relative; z-index: 1; }
        .ah-stat {
            display: inline-flex; align-items: center; gap: .55rem;
            margin-top: 1.1rem; padding: .5rem .85rem;
            border: 1px solid rgba(255,255,255,.17); border-radius: 999px;
            background: rgba(255,255,255,.08); color: #e9eff7; font-size: .82rem;
        }
        .ah-search { display:flex; gap:.55rem; max-width:470px; }
        .ah-search .form-control { min-height:46px; border-color:var(--ah-line); }
        .ah-layout { display:grid; grid-template-columns:minmax(0, 1fr) 285px; gap:1.25rem; align-items:start; }
        .ah-feed { display:grid; gap:1rem; }
        .ah-card {
            position:relative; overflow:hidden; border:1px solid var(--ah-line);
            border-radius:18px; background:#fff; box-shadow:0 6px 20px rgba(26,39,61,.045);
            transition:transform .2s ease, box-shadow .2s ease;
            margin-bottom: 20px;
            /* scale: 0.985; */
        }
        .ah-card:hover { transform:translateY(-2px); box-shadow:0 14px 32px rgba(26,39,61,.09); }
        .ah-card:before { content:""; position:absolute; inset:0 auto 0 0; width:4px; background:linear-gradient(#e9bd52,#c78f27); }
        .ah-media { position:relative; display:block; overflow:hidden; background:#e9edf3; }
        .ah-media img { display:block; width:100%; max-height:fit-content; aspect-ratio: 20 / 5; object-fit:cover; transition:transform .35s ease; }
        .ah-card:hover .ah-media img { transform:scale(1.015); }
        .ah-media-count { position:absolute; right:1rem; bottom:1rem; padding:.35rem .65rem; border-radius:999px; color:#fff; background:rgba(23,32,51,.82); font-size:.75rem; font-weight:700; }
        .ah-card-body { padding:clamp(1.2rem, 3vw, 1.8rem); padding-left:calc(clamp(1.2rem, 3vw, 1.8rem) + 4px); }
        .ah-card-top { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
        .ah-author { display:flex; align-items:center; gap:.7rem; min-width:0; }
        .ah-author-icon { display:grid; place-items:center; width:38px; height:38px; flex:none; border-radius:12px; color:#8b671d; background:#fbf4df; }
        .ah-author-name { color:var(--ah-ink); font-size:.84rem; font-weight:700; }
        .ah-meta { color:var(--ah-muted); font-size:.75rem; }
        .ah-badge { border-radius:999px; padding:.32rem .62rem; color:#815e17; background:#fcf4dd; font-size:.68rem; font-weight:800; letter-spacing:.06em; white-space:nowrap; }
        .ah-card h2 { margin:0 0 .85rem; color:var(--ah-ink); font-size:clamp(1.2rem,2.4vw,1.55rem); font-weight:750; letter-spacing:-.025em; }
        .ah-content { color:#3e4a5e; font-size:.96rem; line-height:1.8; overflow-wrap:anywhere; }
        .ah-content > :last-child { margin-bottom:0; }
        .ah-content h1,.ah-content h2,.ah-content h3 { color:var(--ah-ink); margin:.8em 0 .4em; font-weight:750; }
        .ah-content p,.ah-content ul,.ah-content ol,.ah-content blockquote,.ah-content pre { margin:0 0 .8em; }
        .ah-content ul,.ah-content ol { padding-left:1.5rem; }
        .ah-content blockquote { border-left:3px solid #d5a83d; padding:.25rem .9rem; color:#667085; background:#faf8f1; }
        .ah-content a { color:#155f9c; text-decoration:underline; text-underline-offset:2px; }
        .ah-content pre { overflow:auto; padding:1rem; border-radius:12px; color:#e7edf5; background:#172033; font-size:.87rem; }
        .ah-content code { padding:.12em .35em; border-radius:5px; background:#edf1f6; color:#35445a; font-size:.88em; }
        .ah-content pre code { padding:0; color:inherit; background:none; }
        .ah-engagement { display:flex; align-items:center; gap:1rem; margin-top:1.1rem; padding-top:.85rem; border-top:1px solid #edf0f4; color:#718096; font-size:.82rem; }
        .ah-engagement a { color:inherit; text-decoration:none; }
        .ah-engagement a:hover { color:#315f9b; }
        .discord-emoji { width:1.5em; height:1.5em; object-fit:contain; vertical-align:middle; }
        .discord-timestamp { border-radius:5px; padding:.1rem .35rem; background:#edf1f6; color:#4c5c72; font-size:.88em; white-space:nowrap; }
        .discord-spoiler { border-radius:4px; padding:0 .18em; color:transparent; background:#222b3a; cursor:pointer; transition:color .15s ease; }
        .discord-spoiler:hover,.discord-spoiler.is-revealed { color:#fff; }
        .ah-aside-card { border:1px solid var(--ah-line); border-radius:18px; padding:1.25rem; background:#fff; box-shadow:0 6px 20px rgba(26,39,61,.04); }
        .ah-aside-card + .ah-aside-card { margin-top:1rem; }
        .ah-aside-title { margin:0 0 .9rem; color:var(--ah-ink); font-size:.9rem; font-weight:800; font-family: 'Courier New', Courier, monospace}
        .ah-aside-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.7rem 0; border-top:1px solid #f0f2f6; color:var(--ah-muted); font-size:.8rem; }
        .ah-aside-row strong { color:var(--ah-ink); }
        .ah-tip { display:flex; gap:.65rem; color:#667085; font-size:.82rem; line-height:1.65; }
        .ah-tip i { margin-top:.2rem; color:var(--ah-gold); }
        .ah-empty { border:1px dashed #cfd7e3; border-radius:18px; padding:3.5rem 1rem; background:#fff; text-align:center; }
        .ah-empty i { color:#b5c0d0; font-size:2rem; }
        @media (max-width: 900px) { .ah-layout { grid-template-columns:1fr; } .ah-aside { display:grid; grid-template-columns:1fr 1fr; gap:1rem; } .ah-aside-card + .ah-aside-card { margin-top:0; } }
        @media (max-width: 575px) { .announcement-hub { padding-top:1rem; } .ah-hero { border-radius:18px; } .ah-aside { grid-template-columns:1fr; } .ah-card-top { align-items:flex-start; } }
    </style>
@endpush

@section('content')
    <main class="announcement-hub">
        <div class="container ah-shell">
            <section class="ah-hero">
                <div class="row align-items-end g-4 position-relative" style="z-index:1">
                    <div class="col-lg">
                        <div class="ah-kicker mb-3"><i class="fa-solid fa-star me-2"></i>LSSD · BẢN TIN NỘI BỘ</div>
                        <h1 class="mb-3">Thông cáo &amp; thông báo</h1>
                        <p class="mb-0">Cập nhật chỉ thị, lịch hoạt động và những thông tin quan trọng dành cho toàn thể nhân sự.</p>
                        <div class="ah-stat"><i class="fa-regular fa-bell"></i>{{ number_format($announcements->total()) }} thông báo được lưu trữ</div>
                    </div>
                    @can('manage-announcements')
                        <div class="col-lg-auto">
                            <a class="btn btn-warning fw-bold px-4 py-3" href="{{ route('admin.announcements.index') }}">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Soạn thông báo
                            </a>
                        </div>
                    @endcan
                </div>
            </section>

            <form class="ah-search mb-4" action="{{ route('announcements.index') }}" method="GET" role="search">
                <input class="form-control" type="search" name="q" value="{{ $search }}"
                    placeholder="Tìm trong tiêu đề và nội dung..." aria-label="Tìm thông báo">
                <button class="btn btn-dark px-3" type="submit" aria-label="Tìm kiếm"><i class="fa-solid fa-magnifying-glass"></i></button>
                @if ($search !== '')
                    <a class="btn btn-outline-secondary d-flex align-items-center" href="{{ route('announcements.index') }}" aria-label="Xóa tìm kiếm">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <div class="ah-layout">
                <section class="ah-feed-" aria-label="Danh sách thông báo"
                    data-interactions-url="{{ route('announcements.interactions.summaries') }}">
                    @forelse ($announcements as $announcement)
                        <article class="ah-card" id="announcement-{{ $announcement->id }}" data-announcement-id="{{ $announcement->id }}">
                            @if ($announcement->image_display_url)
                                <a class="ah-media" href="{{ route('announcements.show', $announcement) }}" aria-label="Xem chi tiết: {{ $announcement->title }}">
                                    <img src="{{ $announcement->image_display_url }}" alt="Ảnh minh họa: {{ $announcement->title }}"
                                        loading="lazy" decoding="async" onerror="this.closest('.ah-media').remove()">
                                    @if (count($announcement->image_display_urls) > 1)
                                        <span class="ah-media-count"><i class="fa-regular fa-images me-1"></i>{{ count($announcement->image_display_urls) }} ảnh</span>
                                    @endif
                                </a>
                            @endif
                            <div class="ah-card-body">
                                <div class="ah-card-top">
                                    <div class="ah-author">
                                        <span class="ah-author-icon"><i class="fa-solid fa-shield-halved"></i></span>
                                        <div>
                                            <div class="ah-author-name">{{ $announcement->author?->employee?->name_ingame ?? $announcement->author?->username ?? 'Ban quản lý' }}</div>
                                            <div class="ah-meta">
                                                <time datetime="{{ $announcement->created_at->toIso8601String() }}">{{ $announcement->created_at->format('d/m/Y · H:i') }}</time>
                                                @if ($announcement->updated_at->gt($announcement->created_at->addMinute()))
                                                    <span> · Đã cập nhật {{ $announcement->updated_at->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if ($announcement->created_at->greaterThan(now()->subDays(3)))
                                        <span class="ah-badge"><i class="fa-solid fa-sparkles me-1"></i>MỚI</span>
                                    @else
                                        <span class="ah-badge">THÔNG BÁO</span>
                                    @endif
                                </div>
                                <h2><a class="text-decoration-none text-reset" href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a></h2>
                                <div class="ah-content">{!! $announcement->rendered_content !!}</div>
                                <div class="ah-engagement" aria-label="Mức độ tương tác">
                                    <a href="{{ route('announcements.show', $announcement) }}#reactions">
                                        <span aria-hidden="true">👍 ❤️ 😂</span>
                                        <span data-list-reactions>{{ number_format($announcement->reactions_count) }}</span> cảm xúc
                                    </a>
                                    <a href="{{ route('announcements.show', $announcement) }}#comments">
                                        <i class="fa-regular fa-comment me-1"></i><span data-list-comments>{{ number_format($announcement->comments_count) }}</span> bình luận
                                    </a>
                                </div>
                                <a class="btn btn-sm btn-outline-secondary mt-3" href="{{ route('announcements.show', $announcement) }}">
                                    <i class="fa-regular fa-eye me-1"></i>Xem chi tiết
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="ah-empty">
                            <i class="fa-regular fa-folder-open d-block mb-3"></i>
                            <h2 class="h5">{{ $search !== '' ? 'Không tìm thấy thông báo phù hợp' : 'Chưa có thông báo nào' }}</h2>
                            <p class="text-muted mb-0">{{ $search !== '' ? 'Thử từ khóa khác hoặc xóa bộ lọc tìm kiếm.' : 'Thông báo mới sẽ xuất hiện tại đây.' }}</p>
                        </div>
                    @endforelse
                    
                    @if ($announcements->hasPages())
                        <div>{{ $announcements->links() }}</div>
                    @endif
                </section>

                <aside class="ah-aside">
                    <section class="ah-aside-card">
                        <h2 class="ah-aside-title"><i class="fa-solid fa-box-archive me-2 text-warning"></i>Kho lưu trữ</h2>
                        <div class="ah-aside-row"><span>Tổng số thông báo</span><strong>{{ number_format($announcements->total()) }}</strong></div>
                        <div class="ah-aside-row"><span>Đang hiển thị</span><strong>{{ $announcements->count() }}</strong></div>
                        <div class="ah-aside-row"><span>Trang</span><strong>{{ $announcements->currentPage() }} / {{ max(1, $announcements->lastPage()) }}</strong></div>
                    </section>
                    <section class="ah-aside-card">
                        <h2 class="ah-aside-title"><i class="fa-solid fa-circle-info me-2 text-warning"></i>Lưu ý</h2>
                        <div class="ah-tip"><i class="fa-solid fa-check"></i><span>Kiểm tra thông báo thường xuyên để không bỏ lỡ lịch hoạt động và chỉ đạo mới.</span></div>
                        <hr>
                        <div class="ah-tip"><i class="fa-brands fa-discord"></i><span>Thông báo chính thức chi tiết hiển thị trên discord Thông báo LSSD.</span></div>
                    </section>
                </aside>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const announcementFeed = document.querySelector('.ah-feed-');
        const refreshAnnouncementCounts = async () => {
            if (!announcementFeed || document.hidden) return;
            const ids = [...announcementFeed.querySelectorAll('[data-announcement-id]')]
                .map(card => card.dataset.announcementId);
            if (!ids.length) return;

            try {
                const url = new URL(announcementFeed.dataset.interactionsUrl, window.location.origin);
                ids.forEach(id => url.searchParams.append('ids[]', id));
                const response = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) throw new Error(`Tải số liệu tương tác thất bại (${response.status}).`);
                (await response.json()).forEach(item => {
                    const card = announcementFeed.querySelector(`[data-announcement-id="${item.id}"]`);
                    if (!card) return;
                    card.querySelector('[data-list-reactions]').textContent = new Intl.NumberFormat('vi-VN').format(item.reactions_count);
                    card.querySelector('[data-list-comments]').textContent = new Intl.NumberFormat('vi-VN').format(item.comments_count);
                });
            } catch (error) {
                console.error('Không thể cập nhật số liệu tương tác thông báo:', error);
            }
        };
        refreshAnnouncementCounts();
        window.setInterval(refreshAnnouncementCounts, 5000);

        document.addEventListener('click', event => {
            const spoiler = event.target.closest('.discord-spoiler');
            if (spoiler) spoiler.classList.toggle('is-revealed');
        });
        document.addEventListener('keydown', event => {
            if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.discord-spoiler')) {
                event.preventDefault();
                event.target.classList.toggle('is-revealed');
            }
        });
    </script>
@endpush
