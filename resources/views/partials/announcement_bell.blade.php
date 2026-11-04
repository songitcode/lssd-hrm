@auth
    <style>
        .announcement-bell { position:relative; flex:none; }
        .announcement-bell-trigger {
            position:relative; display:grid; place-items:center; width:42px; height:42px;
            border:1px solid rgba(148,163,184,.3); border-radius:13px;
            color:#334155; background:#fff; transition:background .18s ease, transform .18s ease;
        }
        .announcement-bell-trigger:hover { transform:translateY(-1px); background:#f8fafc; }
        .announcement-bell-trigger.has-unread { color:#9a6b0c; border-color:#edd28d; background:#fffaf0; }
        .announcement-bell-badge {
            position:absolute; top:-6px; right:-7px; display:grid; place-items:center;
            min-width:19px; height:19px; padding:0 5px; border:2px solid #fff; border-radius:999px;
            color:#fff; background:#dc3545; font:700 10px/1 system-ui,sans-serif;
        }
        .announcement-bell-menu {
            width:min(370px,calc(100vw - 24px)); max-height:min(480px,75vh); overflow:auto;
            padding:0; border:1px solid #e6eaf0; border-radius:15px;
            box-shadow:0 18px 48px rgba(20,32,51,.18);
        }
        .announcement-bell-heading { display:flex; justify-content:space-between; align-items:center; padding:14px 16px; border-bottom:1px solid #edf0f4; }
        .announcement-bell-heading strong { color:#172033; font-size:.88rem; }
        .announcement-bell-heading span { color:#728096; font-size:.72rem; }
        .announcement-bell-item { display:block; padding:0; border-bottom:1px solid #f0f2f5; }
        .announcement-bell-item:last-child { border-bottom:0; }
        .announcement-bell-item form { margin:0; }
        .announcement-bell-item button {
            display:block; width:100%; padding:12px 16px; border:0; text-align:left; background:#fff;
            transition:background .16s ease;
        }
        .announcement-bell-link {
            display:block; width:100%; padding:12px 16px; color:inherit; text-align:left; text-decoration:none; background:#fff;
            transition:background .16s ease;
        }
        .announcement-bell-link:hover { background:#f7f9fc; }
        .announcement-bell-item button:hover { background:#f7f9fc; }
        .announcement-bell-title { display:block; overflow:hidden; color:#202d42; font-size:.82rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
        .announcement-bell-excerpt { display:-webkit-box; overflow:hidden; margin-top:3px; color:#748096; font-size:.73rem; line-height:1.45; -webkit-box-orient:vertical; -webkit-line-clamp:2; }
        .announcement-bell-meta { display:flex; justify-content:space-between; gap:8px; margin-top:7px; color:#8a94a5; font-size:.67rem; }
        .announcement-bell-empty { padding:24px 16px; color:#718096; text-align:center; font-size:.8rem; }
        .announcement-bell-footer { padding:10px 16px; border-top:1px solid #edf0f4; text-align:center; }
        .announcement-bell-footer a { color:#866116; font-size:.77rem; font-weight:700; text-decoration:none; }
        @keyframes announcement-ring { 0%,100%{transform:rotate(0)} 15%{transform:rotate(14deg)} 30%{transform:rotate(-12deg)} 45%{transform:rotate(8deg)} 60%{transform:rotate(-5deg)} }
        .announcement-bell-trigger.has-unread i { animation:announcement-ring 1.8s ease-in-out infinite; transform-origin:top center; }
        @media(prefers-reduced-motion:reduce) { .announcement-bell-trigger.has-unread i { animation:none; } }
    </style>

    <div class="dropdown announcement-bell">
        <button class="announcement-bell-trigger {{ $unreadAnnouncementCount > 0 ? 'has-unread' : '' }}"
            type="button" id="announcementBell" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            aria-expanded="false" aria-label="Thông báo mới: {{ $unreadAnnouncementCount }}">
            <i class="fa-regular fa-bell"></i>
            <span class="announcement-bell-badge" data-announcement-bell-badge
                @if ($unreadAnnouncementCount === 0) hidden @endif>{{ $unreadAnnouncementCount > 99 ? '99+' : $unreadAnnouncementCount }}</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end announcement-bell-menu" aria-labelledby="announcementBell">
            <div class="announcement-bell-heading">
                <strong><i class="fa-regular fa-bell me-2 text-warning"></i>Thông báo mới</strong>
                <span data-announcement-bell-count>{{ number_format($unreadAnnouncementCount) }} chưa xem</span>
            </div>
            <div data-announcement-bell-items>
                @forelse ($unreadAnnouncements as $announcement)
                    <div class="announcement-bell-item">
                        <form action="{{ route('announcements.read', $announcement) }}" method="POST">
                            @csrf
                            <button type="submit">
                                <span class="announcement-bell-title">{{ $announcement->title }}</span>
                                <span class="announcement-bell-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 110) }}</span>
                                <span class="announcement-bell-meta">
                                    <span>{{ $announcement->author?->employee?->name_ingame ?? $announcement->author?->username ?? 'Ban quản lý' }}</span>
                                    <time datetime="{{ $announcement->created_at->toIso8601String() }}">{{ $announcement->created_at->diffForHumans() }}</time>
                                </span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="announcement-bell-empty" data-announcement-bell-empty>
                        <i class="fa-regular fa-circle-check me-1"></i>Bạn đã xem hết thông báo.
                    </div>
                @endforelse
            </div>
            <div class="announcement-bell-footer">
                <a href="{{ route('announcements.index') }}">Xem kho thông báo <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const root = document.currentScript?.previousElementSibling;
            if (!root || root.dataset.announcementBellReady) return;
            root.dataset.announcementBellReady = '1';

            const badge = root.querySelector('[data-announcement-bell-badge]');
            const trigger = root.querySelector('.announcement-bell-trigger');
            const countLabel = root.querySelector('[data-announcement-bell-count]');
            const items = root.querySelector('[data-announcement-bell-items]');
            const feedUrl = @json(route('announcements.notifications'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const render = data => {
                const count = Number(data.count) || 0;
                badge.textContent = count > 99 ? '99+' : String(count);
                badge.hidden = count === 0;
                trigger.classList.toggle('has-unread', count > 0);
                trigger.setAttribute('aria-label', 'Thông báo mới: ' + count);
                countLabel.textContent = count.toLocaleString('vi-VN') + ' chưa xem';
                items.replaceChildren();

                if (!data.items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'announcement-bell-empty';
                    empty.innerHTML = '<i class="fa-regular fa-circle-check me-1"></i>Bạn đã xem hết thông báo.';
                    items.append(empty);
                    return;
                }

                data.items.forEach(item => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'announcement-bell-item';
                    const isPost = item.method === 'POST';
                    const action = document.createElement(isPost ? 'button' : 'a');
                    if (isPost) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = item.url;
                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = csrfToken;
                        action.type = 'submit';
                        form.append(token, action);
                        wrapper.append(form);
                    } else {
                        action.href = item.url;
                        action.className = 'announcement-bell-link';
                        wrapper.append(action);
                    }
                    const title = document.createElement('span');
                    title.className = 'announcement-bell-title';
                    title.textContent = item.title;
                    const excerpt = document.createElement('span');
                    excerpt.className = 'announcement-bell-excerpt';
                    excerpt.textContent = item.excerpt;
                    const meta = document.createElement('span');
                    meta.className = 'announcement-bell-meta';
                    const author = document.createElement('span');
                    author.textContent = item.author;
                    const time = document.createElement('time');
                    time.textContent = item.time;
                    meta.append(author, time);
                    action.append(title, excerpt, meta);
                    items.append(wrapper);
                });
            };

            const refresh = async () => {
                try {
                    const response = await fetch(feedUrl, {
                        credentials: 'same-origin',
                        headers: { Accept: 'application/json' }
                    });
                    if (!response.ok) throw new Error('Không tải được thông báo (' + response.status + ').');
                    render(await response.json());
                } catch (error) {
                    console.error('Announcement bell refresh failed:', error);
                }
            };

            refresh();
            window.setInterval(refresh, 30000);
        })();
    </script>
@endauth
