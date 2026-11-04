@extends('layouts.app')

@section('title', $announcement->title)

@push('styles')
    <style>
        .announcement-detail { min-height:75vh; padding:clamp(1.25rem,4vw,2.5rem) 0 4rem; background:linear-gradient(180deg,#f3f6fb 0,#f8f9fc 45%,#f3f6fb 100%); }
        .ad-shell { max-width:900px; }
        .ad-back { display:inline-flex; align-items:center; gap:.5rem; margin-bottom:1rem; color:#5f6d80; text-decoration:none; font-size:.88rem; font-weight:700; }
        .ad-card { overflow:hidden; border:1px solid rgba(220,227,237,.9); border-radius:24px; background:#fff; box-shadow:0 18px 54px rgba(29,46,74,.08); }
        .ad-body { padding:clamp(1.2rem,4vw,2.6rem); }
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
        .ad-social { margin-top:1.8rem; padding-top:1.25rem; border-top:1px solid #edf0f4; }
        .ad-social-summary { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; color:#667085; font-size:.86rem; }
        .ad-social-summary [data-reaction-emojis] { margin-right:.35rem; letter-spacing:-.3rem; }
        .ad-reactions { display:flex; flex-wrap:wrap; gap:.55rem; }
        .ad-reaction-form { margin:0; }
        .ad-reaction { display:inline-flex; align-items:center; gap:.4rem; min-height:40px; padding:.4rem .8rem; border:1px solid #e4e9f1; border-radius:13px; color:#4b5565; background:#fff; font-size:.84rem; font-weight:650; box-shadow:0 2px 6px rgba(20,33,55,.03); transition:transform .15s ease,background .15s ease,border-color .15s ease,color .15s ease; }
        .ad-reaction:hover { transform:translateY(-1px); border-color:#b9c9df; background:#f4f7fb; }
        .ad-reaction.is-selected { border-color:#b7c9e6; color:#1769aa; background:#edf4ff; }
        .ad-reaction-emoji { font-size:1.1rem; line-height:1; }
        .ad-login-hint { margin:.75rem 0 0; color:#718096; font-size:.85rem; }
        .ad-comments { margin-top:1.7rem; padding:clamp(1rem,3vw,1.5rem); border:1px solid #edf0f4; border-radius:20px; background:linear-gradient(145deg,#fafbfd,#f5f7fb); }
        .ad-comments-title { display:flex; align-items:center; justify-content:space-between; margin:0 0 1rem; color:#172033; font-size:1.05rem; font-weight:800; }
        .ad-comment-form { margin-bottom:1.4rem; padding:1rem; border:1px solid #e8edf4; border-radius:17px; background:#fff; box-shadow:0 5px 16px rgba(20,33,55,.035); }
        .ad-comment-form textarea,.ad-reply-form textarea { border-color:#e4e9f1; border-radius:13px; background:#fbfcfe; resize:vertical; }
        .ad-comment-form textarea:focus,.ad-reply-form textarea:focus { border-color:#91b4e3; background:#fff; box-shadow:0 0 0 .2rem rgba(51,112,187,.1); }
        .ad-comment-list { display:grid; gap:.85rem; }
        .ad-comment-batch { display:grid; gap:.85rem; }
        .ad-comment { position:relative; scroll-margin-top:6rem; }
        .ad-comment-card { padding:1rem 1.05rem; border:1px solid #e8edf4; border-radius:16px; background:#fff; box-shadow:0 4px 14px rgba(20,33,55,.035); }
        .ad-comment-head { display:flex; align-items:center; gap:.65rem; color:#253148; font-size:.86rem; }
        .ad-comment-avatar { display:grid; place-items:center; width:34px; height:34px; flex:none; border-radius:11px; color:#315d91; background:linear-gradient(145deg,#e9f1fb,#f0eafa); font-size:.83rem; font-weight:800; }
        .ad-comment-identity { display:grid; gap:.1rem; }
        .ad-comment-identity strong { color:#253148; font-size:.84rem; font-weight:750; }
        .ad-comment-identity time { color:#8792a2; font-size:.71rem; }
        .ad-comment-body { margin:.7rem 0 0 2.65rem; color:#3e4a5e; font-size:.91rem; line-height:1.65; overflow-wrap:anywhere; }
        .ad-comment-body p { margin:0; white-space:pre-wrap; word-break:break-word; }
        .ad-comment-expand { display:inline-block; margin-top:.3rem; padding:0; border:0; color:#4778b5; background:none; font-size:.82rem; font-weight:750; }
        .ad-comment-expand:hover { color:#24558f; text-decoration:underline; }
        .ad-comment-empty { padding:1.4rem 1rem; border:1px dashed #d5deea; border-radius:15px; color:#718096; background:rgba(255,255,255,.7); text-align:center; font-size:.9rem; }
        .ad-comment-actions { display:flex; align-items:center; gap:.9rem; margin:.6rem 0 0 2.65rem; }
        .ad-inline-form { display:inline; margin:0; }
        .ad-comment-action { padding:0; border:0; color:#6b7789; background:none; font-size:.78rem; font-weight:700; }
        .ad-comment-action:hover { color:#1769aa; text-decoration:underline; }
        .ad-comment-delete:hover { color:#bd3544; }
        .ad-replies { display:grid; gap:.7rem; margin:.7rem 0 0 1.4rem; padding-left:1rem; border-left:2px solid #dfe7f1; }
        .ad-reply-form { margin:.7rem 0 0 2.65rem; padding:.75rem; border:1px solid #e8edf4; border-radius:14px; background:#f8fafd; }
        .ad-reply-submit { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-top:.5rem; }
        .ad-reply-submit span { color:#8994a4; font-size:.68rem; }
        .ad-load-comments { display:block; width:100%; margin-top:1rem; padding:.75rem 1rem; border:1px solid #e1e8f1; border-radius:13px; color:#3d5f89; background:#fff; font-size:.84rem; font-weight:750; transition:background .15s ease,transform .15s ease; }
        .ad-load-comments:hover { transform:translateY(-1px); background:#f1f6fc; }
        .discord-emoji { width:1.5em; height:1.5em; object-fit:contain; vertical-align:middle; }
        .discord-timestamp { border-radius:5px; padding:.1rem .35rem; background:#edf1f6; color:#4c5c72; font-size:.88em; white-space:nowrap; }
        .discord-spoiler { border-radius:4px; padding:0 .18em; color:transparent; background:#222b3a; cursor:pointer; }
        .discord-spoiler:hover,.discord-spoiler.is-revealed { color:#fff; }
        @media(max-width:575px) {
            .announcement-detail { padding-top:1rem; }
            .ad-card { border-radius:17px; }
            .ad-gallery { grid-template-columns:1fr; }
            .ad-comments { padding:.85rem; border-radius:16px; }
            .ad-comment-card { padding:.85rem; }
            .ad-comment-body,.ad-comment-actions,.ad-reply-form { margin-left:0; }
            .ad-replies { margin-left:.55rem; padding-left:.65rem; }
            .ad-reaction { min-height:38px; padding:.35rem .55rem; font-size:.78rem; }
        }
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

                    <section class="ad-social" id="reactions" aria-label="Cảm xúc"
                        data-interactions-url="{{ route('announcements.interactions.state', $announcement) }}">
                        <div class="ad-social-summary" id="ad-reaction-summary">
                            <span>
                                <span data-reaction-emojis>
                                    @foreach ($reactionTypes as $type => $reaction)
                                        @if (($reactionCounts[$type] ?? 0) > 0)
                                            <span title="{{ $reaction['label'] }}">{{ $reaction['emoji'] }}</span>
                                        @endif
                                    @endforeach
                                </span>
                                <span data-total-reactions>{{ number_format($announcement->reactions_count) }}</span> cảm xúc
                            </span>
                            <a class="text-decoration-none text-secondary" href="#comments">
                                <span data-total-comments>{{ number_format($announcement->comments_count) }}</span> bình luận
                            </a>
                        </div>
                        @auth
                            <div class="ad-reactions">
                                @foreach ($reactionTypes as $type => $reaction)
                                    <form class="ad-reaction-form" action="{{ route('announcements.reactions.toggle', $announcement) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <button class="ad-reaction {{ $currentReaction === $type ? 'is-selected' : '' }}" type="submit"
                                            aria-pressed="{{ $currentReaction === $type ? 'true' : 'false' }}"
                                            title="{{ $reaction['label'] }}" data-reaction-button="{{ $type }}">
                                            <span class="ad-reaction-emoji" aria-hidden="true">{{ $reaction['emoji'] }}</span>
                                            <span>{{ $reaction['label'] }}</span>
                                            <span data-reaction-count>{{ number_format($reactionCounts[$type] ?? 0) }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @else
                            <p class="ad-login-hint"><a href="{{ route('login') }}">Đăng nhập</a> để bày tỏ cảm xúc hoặc bình luận.</p>
                        @endauth
                    </section>

                    <section class="ad-comments" id="comments" aria-label="Bình luận">
                        <h2 class="ad-comments-title">
                            <span>Bình luận</span>
                            <span class="text-secondary fs-6 fw-normal" data-total-comments>{{ number_format($announcement->comments_count) }}</span>
                        </h2>
                        @auth
                            <form class="ad-comment-form" action="{{ route('announcements.comments.store', $announcement) }}" method="POST" data-interaction-form>
                                @csrf
                                <label class="form-label fw-semibold" for="comment-body">Viết bình luận</label>
                                <textarea class="form-control @error('body') is-invalid @enderror" id="comment-body" name="body"
                                    rows="3" maxlength="2000" required placeholder="Chia sẻ ý kiến của bạn...">{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <button class="btn btn-primary btn-sm mt-2" type="submit">
                                    <i class="fa-regular fa-paper-plane me-1"></i>Gửi bình luận
                                </button>
                            </form>
                        @endauth

                        @include('announcements.partials.comments', ['comments' => $comments, 'announcement' => $announcement])
                    </section>
                </div>
            </article>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const interactions = document.getElementById('reactions');
        const formatCount = value => new Intl.NumberFormat('vi-VN').format(value);
        let isRefreshing = false;
        const replaceFirstCommentBatch = state => {
            const openReplyForms = [...document.querySelectorAll('.ad-reply-form:not(.d-none)')];
            if (openReplyForms.length) return;
            const expandedComments = new Set(
                [...document.querySelectorAll('[data-comment-expand][aria-expanded="true"]')]
                    .map(button => button.closest('[data-comment-id]')?.dataset.commentId)
            );
            const template = document.createElement('template');
            template.innerHTML = state.comments_html;
            const incomingContent = template.content.querySelector('#ad-comment-content');
            const currentContent = document.getElementById('ad-comment-content');
            if (!incomingContent || !currentContent) return;

            const currentList = currentContent.querySelector('.ad-comment-list');
            const incomingList = incomingContent.querySelector('.ad-comment-list');
            const currentFirstBatch = currentList?.querySelector('.ad-comment-batch');
            const incomingFirstBatch = incomingList?.querySelector('.ad-comment-batch');
            const hasLoadedOlderBatches = currentList?.querySelectorAll('.ad-comment-batch').length > 1;
            if (currentFirstBatch && incomingFirstBatch) {
                currentFirstBatch.replaceWith(incomingFirstBatch);
            } else if (currentFirstBatch && !incomingFirstBatch) {
                if (currentList.querySelectorAll('.ad-comment-batch').length === 1) {
                    currentContent.replaceWith(incomingContent);
                } else {
                    currentFirstBatch.remove();
                }
            } else if (!currentList && incomingList) {
                currentContent.replaceWith(incomingContent);
            }
            expandedComments.forEach(id => {
                const comment = document.querySelector(`[data-comment-id="${id}"]`);
                const button = comment?.querySelector('[data-comment-expand]');
                if (!button) return;
                button.click();
            });
            if (!hasLoadedOlderBatches) {
                const loadMore = currentContent.querySelector('[data-comments-cursor]');
                if (!state.comments_next_cursor) {
                    loadMore?.remove();
                } else if (loadMore) {
                    loadMore.dataset.commentsCursor = state.comments_next_cursor;
                }
            }
        };

        const refreshInteractions = async () => {
            if (isRefreshing) return;
            isRefreshing = true;
            try {
                const stateUrl = new URL(interactions.dataset.interactionsUrl, window.location.origin);
                const commentsPage = new URL(window.location.href).searchParams.get('comments');
                if (commentsPage) stateUrl.searchParams.set('comments', commentsPage);
                const response = await fetch(stateUrl, {
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error(`Tải tương tác thất bại (${response.status}).`);
                const state = await response.json();

                document.querySelectorAll('[data-total-comments]').forEach(element => {
                    element.textContent = formatCount(state.comments_count);
                });
                document.querySelector('[data-total-reactions]').textContent = formatCount(state.reactions_count);

                const emojiList = document.querySelector('[data-reaction-emojis]');
                emojiList.replaceChildren();
                document.querySelectorAll('[data-reaction-button]').forEach(button => {
                    const type = button.dataset.reactionButton;
                    const count = state.reactions[type] || 0;
                    button.querySelector('[data-reaction-count]').textContent = formatCount(count);
                    button.classList.toggle('is-selected', state.current_reaction === type);
                    button.setAttribute('aria-pressed', state.current_reaction === type ? 'true' : 'false');
                    if (count > 0) {
                        const emoji = document.createElement('span');
                        emoji.title = button.title;
                        emoji.textContent = button.querySelector('.ad-reaction-emoji').textContent;
                        emojiList.append(emoji);
                    }
                });
                replaceFirstCommentBatch(state);
            } catch (error) {
                console.error('Không thể cập nhật tương tác thông báo:', error);
            } finally {
                isRefreshing = false;
            }
        };

        document.addEventListener('submit', async event => {
            const form = event.target.closest('.ad-reaction-form, [data-interaction-form]');
            if (!form) return;
            event.preventDefault();

            if (form.querySelector('textarea') && !form.querySelector('textarea').value.trim()) return;

            if (form.querySelector('input[name="_method"]')?.value === 'DELETE' &&
                !window.confirm('Bạn có chắc muốn xóa bình luận này?')) return;

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const result = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const message = result.message || Object.values(result.errors || {}).flat()[0] ||
                        `Không thể hoàn tất thao tác (${response.status}).`;
                    throw new Error(message);
                }
                if (form.classList.contains('ad-comment-form')) form.reset();
                if (form.classList.contains('ad-reply-form')) {
                    form.reset();
                    form.classList.add('d-none');
                }
                if (form.querySelector('input[name="_method"]')?.value === 'DELETE') {
                    form.closest('[data-comment-id]')?.remove();
                }
                await refreshInteractions();
            } catch (error) {
                window.alert(error.message);
            } finally {
                if (submitButton) submitButton.disabled = false;
            }
        });

        document.addEventListener('click', async event => {
            const expandComment = event.target.closest('[data-comment-expand]');
            if (expandComment) {
                const text = expandComment.closest('[data-comment-text]');
                const preview = text.querySelector('[data-comment-preview]');
                const full = text.querySelector('[data-comment-full]');
                const expanded = expandComment.getAttribute('aria-expanded') === 'true';
                preview.classList.toggle('d-none', !expanded);
                full.classList.toggle('d-none', expanded);
                expandComment.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                expandComment.textContent = expanded ? 'Xem thêm' : 'Thu gọn';
                return;
            }

            const loadMore = event.target.closest('[data-comments-cursor]');
            if (loadMore) {
                loadMore.disabled = true;
                loadMore.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải...';
                try {
                    const url = new URL(loadMore.dataset.commentsUrl, window.location.origin);
                    url.searchParams.set('comments_limit', '5');
                    url.searchParams.set('comments_cursor', loadMore.dataset.commentsCursor);
                    const response = await fetch(url, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error(`Tải thêm bình luận thất bại (${response.status}).`);
                    const state = await response.json();
                    const template = document.createElement('template');
                    template.innerHTML = state.comments_html;
                    const incomingBatch = template.content.querySelector('.ad-comment-batch');
                    if (incomingBatch) {
                        const commentList = document.querySelector('.ad-comment-list');
                        const visibleIds = new Set([...commentList.querySelectorAll('[data-comment-id]')]
                            .map(comment => comment.dataset.commentId));
                        incomingBatch.querySelectorAll('[data-comment-id]').forEach(comment => {
                            if (visibleIds.has(comment.dataset.commentId)) comment.remove();
                        });
                        if (incomingBatch.children.length > 0) commentList.append(incomingBatch);
                    }
                    if (state.comments_next_cursor) {
                        loadMore.dataset.commentsCursor = state.comments_next_cursor;
                        loadMore.disabled = false;
                        loadMore.innerHTML = '<i class="fa-solid fa-chevron-down me-2"></i>Xem thêm 5 bình luận';
                    } else {
                        loadMore.remove();
                    }
                } catch (error) {
                    loadMore.disabled = false;
                    loadMore.innerHTML = '<i class="fa-solid fa-rotate-right me-2"></i>Thử tải lại bình luận';
                    console.error(error);
                }
                return;
            }

            const replyToggle = event.target.closest('[data-reply-toggle]');
            if (!replyToggle) return;
            const form = document.getElementById(`reply-form-${replyToggle.dataset.replyToggle}`);
            if (!form) return;
            form.classList.toggle('d-none');
            if (!form.classList.contains('d-none')) form.querySelector('textarea').focus();
        });

        document.addEventListener('keydown', event => {
            if (event.key !== 'Enter' || event.shiftKey || !event.target.matches('.ad-reply-form textarea')) return;
            event.preventDefault();
            event.target.form.requestSubmit();
        });

        refreshInteractions();
        window.setInterval(() => {
            if (!document.hidden) refreshInteractions();
        }, 5000);

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
