@extends('layouts.app')

@section('title', $announcement->title)

@push('styles')
    <style>
        .announcement-detail { min-height:75vh; padding:2rem 0 4rem; }
        .ad-shell { max-width:940px; }
        .ad-back { display:inline-flex; align-items:center; gap:.5rem; margin-bottom:1rem; color:#5f6d80; text-decoration:none; font-size:.88rem; font-weight:700; }
        .ad-card { overflow:hidden; border:1px solid #e7ebf1; border-radius:20px; background:#fff; box-shadow:0 10px 32px rgba(26,39,61,.07); }
        .ad-body { padding:clamp(1.2rem,4vw,2.4rem); }
        .ad-author { display:flex; align-items:center; gap:.7rem; margin-bottom:1.4rem; color:#172033; font-size:.86rem; font-weight:750; }
        .ad-author-icon { display:grid; place-items:center; width:40px; height:40px; border-radius:12px; color:#8b671d; background:#fbf4df; }
        .ad-meta { margin-top:.15rem; color:#718096; font-size:.75rem; font-weight:400; }
        .ad-title { margin:0 0 1.4rem; color:#172033; font-size:clamp(1.65rem,4vw,2.5rem); font-weight:800; letter-spacing:-.04em; overflow-wrap:anywhere; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        .ad-gallery { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr)); gap:.65rem; margin:0 0 1.7rem; }
        .ad-gallery a { display:grid; place-items:center; overflow:hidden; min-height:190px; border-radius:12px;/* background:#e9edf3;*/ }
        .ad-gallery img { display:block; width:100%; max-height:620px; object-fit:contain; }
        .ad-content { color:#3e4a5e; font-size:1rem; line-height:1.85; overflow-wrap:anywhere; }
        .ad-content > :last-child { margin-bottom:0; }
        .ad-content h1,.ad-content h2,.ad-content h3 { color:#172033; margin:.8em 0 .4em; font-weight:750; }
        .ad-content p,.ad-content ul,.ad-content ol,.ad-content blockquote,.ad-content pre { margin:0 0 .8em; }
        .ad-content ul,.ad-content ol { padding-left:1.5rem; }
        .ad-content blockquote { border-left:3px solid #d5a83d; padding:.25rem .9rem; color:#667085; background:#faf8f1; }
        .ad-content a { color:#155f9c; text-decoration:underline; text-underline-offset:2px; }
        .ad-content pre { overflow:auto; padding:1rem; border-radius:12px; color:#e7edf5; background:#172033; font-size:.87rem; }
        .ad-content code { padding:.12em .35em; border-radius:5px; background:#edf1f6; color:#35445a; font-size:.88em; }
        .ad-content pre code { padding:0; color:inherit; background:none; }
        .discord-emoji { width:1.5em; height:1.5em; object-fit:contain; vertical-align:middle; }
        .discord-timestamp { border-radius:5px; padding:.1rem .35rem; background:#edf1f6; color:#4c5c72; font-size:.88em; white-space:nowrap; }
        .discord-spoiler { border-radius:4px; padding:0 .18em; color:transparent; background:#222b3a; cursor:pointer; }
        .discord-spoiler:hover,.discord-spoiler.is-revealed { color:#fff; }
        @media(max-width:575px) { .announcement-detail { padding-top:1rem; } .ad-card { border-radius:16px; } .ad-gallery { grid-template-columns:1fr; } }
    </style>
@endpush

@section('content')
    <main class="announcement-detail">
        <div class="container ad-shell">
            <a class="ad-back" href="{{ route('announcements.index') }}">
                <i class="fa-solid fa-arrow-left"></i>Quay lại danh sách thông báo
            </a>
            <article class="ad-card">
                <div class="ad-body">
                    <div class="ad-author">
                        <span class="ad-author-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <div>
                            <div>{{ $announcement->author?->employee?->name_ingame ?? $announcement->author?->username ?? 'Ban quản lý' }}</div>
                            <div class="ad-meta">
                                <time datetime="{{ $announcement->created_at->toIso8601String() }}">{{ $announcement->created_at->format('d/m/Y · H:i') }}</time>
                                @if ($announcement->updated_at->gt($announcement->created_at->addMinute()))
                                    <span> · Đã cập nhật {{ $announcement->updated_at->format('d/m/Y · H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h1 class="ad-title">{{ $announcement->title }}</h1>

                    @if ($announcement->image_display_urls !== [])
                        <div class="ad-gallery" aria-label="Hình ảnh thông báo">
                            @foreach ($announcement->image_display_urls as $imageUrl)
                                <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $imageUrl }}" alt="Ảnh minh họa: {{ $announcement->title }}" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="ad-content">{!! $announcement->rendered_content !!}</div>
                </div>
            </article>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
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
