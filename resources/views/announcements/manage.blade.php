@extends('layouts.admin')

@section('title', 'Quản lý thông báo')

@push('styles')
    <style>
        .announce-admin { --aa-ink:#172033; --aa-muted:#748096; --aa-line:#e6eaf0; color:var(--aa-ink); }
        .aa-wrap { max-width:1420px; margin:auto; padding:clamp(1rem,3vw,2rem); }
        .aa-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.35rem; }
        .aa-eyebrow { color:#a5791d; font-size:.7rem; font-weight:800; letter-spacing:.16em; }
        .aa-heading h1 { margin:.25rem 0; font-size:clamp(1.55rem,3vw,2.1rem); font-weight:800; letter-spacing:-.04em; }
        .aa-heading p { color:var(--aa-muted); margin:0; }
        .aa-card { overflow:hidden; border:1px solid var(--aa-line); border-radius:16px; background:#fff; box-shadow:0 7px 24px rgba(27,39,58,.045); }
        .aa-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; margin-bottom:1.25rem; }
        .aa-stat { display:flex; align-items:center; gap:.9rem; padding:1.15rem; }
        .aa-stat-icon { display:grid; place-items:center; width:44px; height:44px; flex:none; border-radius:13px; color:#8d681b; background:#fbf3de; font-size:1.05rem; }
        .aa-stat-label { color:var(--aa-muted); font-size:.75rem; }
        .aa-stat-value { margin-top:.12rem; font-size:1.2rem; font-weight:800; }
        .aa-section-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.25rem; border-bottom:1px solid var(--aa-line); }
        .aa-section-head h2 { margin:0; font-size:1rem; font-weight:800; }
        .aa-section-head p { margin:.2rem 0 0; color:var(--aa-muted); font-size:.78rem; }
        .aa-form-body { padding:1.25rem; }
        .aa-form-control { border-color:#dfe4eb; border-radius:9px; }
        .aa-form-control:focus { border-color:#c49b43; box-shadow:0 0 0 .2rem rgba(196,155,67,.15); }
        .aa-image-panel { display:grid; grid-template-columns:minmax(0,1fr) minmax(220px,.7fr); gap:1rem; align-items:start; }
        .aa-image-choice { padding:1rem; border:1px solid var(--aa-line); border-radius:11px; background:#fbfcfe; }
        .aa-image-preview { display:none; overflow:hidden; min-height:160px; max-height:300px; border:1px solid var(--aa-line); border-radius:11px; background:linear-gradient(135deg,#f3f5f8,#e8edf3); }
        .aa-image-preview.is-visible { display:grid; place-items:center; }
        .aa-image-preview img { display:block; width:100%; max-height:300px; object-fit:contain; }
        .announce-admin [hidden] { display:none !important; }
        .aa-image-placeholder { display:grid; place-items:center; min-height:160px; padding:1rem; color:#98a3b3; text-align:center; }
        .aa-image-hint { color:var(--aa-muted); font-size:.76rem; line-height:1.55; }
        .aa-image-error { display:none; color:#dc3545; font-size:.78rem; }
        .aa-editor-grid { display:grid; grid-template-columns:minmax(0,1.1fr) minmax(300px,.9fr); gap:1.2rem; align-items:stretch; }
        .aa-toolbar { display:flex; flex-wrap:wrap; gap:.35rem; padding:.55rem; border:1px solid #dfe4eb; border-bottom:0; border-radius:9px 9px 0 0; background:#f7f8fa; }
        .aa-toolbar button { min-width:34px; border:1px solid transparent; border-radius:6px; padding:.32rem .48rem; color:#4c586c; background:transparent; }
        .aa-toolbar button:hover { border-color:#dbe1e9; background:#fff; }
        .aa-editor { border-top-left-radius:0; border-top-right-radius:0; font-family:Consolas,Monaco,monospace; line-height:1.6; resize:vertical; }
        .aa-preview { min-height:305px; max-height:520px; overflow:auto; padding:1rem 1.1rem; border:1px solid var(--aa-line); border-radius:10px; background:#fcfcfd; color:#3e4a5e; line-height:1.75; overflow-wrap:anywhere; }
        .aa-preview-empty { display:grid; place-items:center; min-height:260px; color:#9aa4b4; text-align:center; }
        .aa-preview blockquote { margin:.5rem 0; padding:.4rem .8rem; border-left:3px solid #d5a83d; background:#faf8f1; }
        .aa-preview pre { overflow:auto; padding:.85rem; border-radius:9px; color:#e7edf5; background:#172033; }
        .aa-preview code { border-radius:4px; padding:.1em .3em; background:#edf1f6; }
        .aa-preview pre code { padding:0; color:inherit; background:none; }
        .aa-preview a { color:#155f9c; }
        .aa-preview .discord-emoji { width:1.45em; height:1.45em; vertical-align:middle; }
        .aa-preview .discord-timestamp { border-radius:5px; padding:.1rem .35rem; background:#edf1f6; font-size:.88em; white-space:nowrap; }
        .aa-preview .discord-spoiler { border-radius:4px; padding:0 .18em; color:transparent; background:#222b3a; cursor:pointer; }
        .aa-preview .discord-spoiler:hover,.aa-preview .discord-spoiler.is-revealed { color:#fff; }
        .aa-help { border:1px solid #f0e2bc; border-radius:10px; padding:.8rem .95rem; color:#6b5b39; background:#fffbef; font-size:.79rem; line-height:1.65; }
        .aa-help code { color:#7f5a0f; }
        .aa-item { padding:1.1rem 1.25rem; border-bottom:1px solid var(--aa-line); }
        .aa-item:last-child { border-bottom:0; }
        .aa-item-title-row { display:flex; align-items:center; gap:.75rem; }
        .aa-item-thumb { width:76px; height:56px; flex:none; border:1px solid var(--aa-line); border-radius:8px; object-fit:cover; background:#f1f3f6; }
        .aa-item-title { margin:0; font-size:.98rem; font-weight:800; }
        .aa-item-meta { margin-top:.28rem; color:var(--aa-muted); font-size:.74rem; }
        .aa-item-preview { display:-webkit-box; overflow:hidden; margin:.75rem 0 0; color:#637086; font-size:.85rem; line-height:1.6; -webkit-box-orient:vertical; -webkit-line-clamp:2; }
        .aa-item summary { color:#325c84; cursor:pointer; font-size:.82rem; font-weight:700; }
        .aa-validation { color:#dc3545; font-size:.8rem; }
        .aa-preview-status { min-height:1.2rem; color:var(--aa-muted); font-size:.73rem; }
        @media(max-width:900px) { .aa-editor-grid { grid-template-columns:1fr; } }
        @media(max-width:620px) { .aa-stats { grid-template-columns:1fr; gap:.55rem; } .aa-stat { padding:.85rem; } .aa-heading { display:block; } .aa-heading > a { margin-top:1rem; } .aa-image-panel { grid-template-columns:1fr; } }
    </style>
@endpush

@section('content')
    <main class="announce-admin">
        <div class="aa-wrap">
            <header class="aa-heading">
                <div>
                    <div class="aa-eyebrow"><i class="fa-solid fa-shield-halved me-1"></i>TRUNG TÂM ĐIỀU HÀNH · LSSD</div>
                    <h1>Quản lý thông báo</h1>
                    <p>Soạn nội dung rõ ràng, xem trước định dạng Discord và quản lý kho thông báo.</p>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('announcements.index') }}" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Xem trang công khai
                </a>
            </header>

            <section class="aa-stats" aria-label="Thống kê thông báo">
                <div class="aa-card aa-stat">
                    <span class="aa-stat-icon"><i class="fa-solid fa-box-archive"></i></span>
                    <div><div class="aa-stat-label">Thông báo đã lưu</div><div class="aa-stat-value">{{ number_format($announcementCount) }}</div></div>
                </div>
                <div class="aa-card aa-stat">
                    <span class="aa-stat-icon"><i class="fa-solid fa-list"></i></span>
                    <div><div class="aa-stat-label">Đang hiển thị tại trang này</div><div class="aa-stat-value">{{ $announcements->count() }}</div></div>
                </div>
                <div class="aa-card aa-stat">
                    <span class="aa-stat-icon"><i class="fa-regular fa-clock"></i></span>
                    <div><div class="aa-stat-label">Thông báo gần nhất</div><div class="aa-stat-value fs-6">{{ $latestAnnouncement ? \Illuminate\Support\Carbon::parse($latestAnnouncement)->format('d/m/Y H:i') : 'Chưa có' }}</div></div>
                </div>
            </section>

            <section class="aa-card mb-4">
                <div class="aa-section-head">
                    <div>
                        <h2><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Soạn thông báo mới</h2>
                        <p>Dùng thanh công cụ hoặc dán nội dung đã định dạng từ Discord.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-light border"><i class="fa-solid fa-eye me-1"></i>Xem trước trực tiếp</span>
                </div>
                <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" data-announcement-editor
                    data-preview-url="{{ route('admin.announcements.preview') }}">
                    @csrf
                    <div class="aa-form-body">
                        <div class="mb-3">
                            <label for="announcement-title" class="form-label fw-semibold">Tiêu đề thông báo</label>
                            <input id="announcement-title" name="title" class="form-control aa-form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" maxlength="255" placeholder="Ví dụ: Lịch họp toàn đơn vị ngày 30/10" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ảnh minh họa <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                            <div class="aa-image-panel">
                                <div class="aa-image-choice">
                                    <label for="announcement-image-url" class="form-label small fw-semibold">Dán liên kết ảnh</label>
                                    <input id="announcement-image-url" name="image_url" type="url" inputmode="url"
                                        class="form-control aa-form-control mb-2 @error('image_url') is-invalid @enderror"
                                        value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                                    @error('image_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    <div class="text-center text-muted small my-2">hoặc</div>
                                    <label for="announcement-image-files" class="form-label small fw-semibold">Tải ảnh từ thiết bị</label>
                                    <input id="announcement-image-files" name="image_files[]" type="file" multiple
                                        class="form-control aa-form-control @error('image_files') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    @error('image_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @error('image_files')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @error('image_files.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    <div class="aa-image-hint mt-2">JPG, PNG hoặc WebP · tối đa 5 MB mỗi ảnh, tối đa 10 ảnh. Chỉ chọn URL hoặc tải tệp lên.</div>
                                    <div class="aa-image-hint" data-image-count aria-live="polite"></div>
                                    <div class="aa-image-error mt-2" data-image-error role="alert"></div>
                                </div>
                                <div class="aa-image-preview is-visible" data-image-preview>
                                    <div class="aa-image-placeholder"><div><i class="fa-regular fa-image fs-3 mb-2"></i><div>Ảnh xem trước</div></div></div>
                                    <img alt="Xem trước ảnh thông báo" data-image-element hidden>
                                </div>
                            </div>
                        </div>

                        <div class="aa-editor-grid">
                            <div>
                                <label for="announcement-content" class="form-label fw-semibold">Nội dung</label>
                                <div class="aa-toolbar" role="toolbar" aria-label="Định dạng nội dung">
                                    <button type="button" data-wrap="**" title="In đậm"><strong>B</strong></button>
                                    <button type="button" data-wrap="*" title="In nghiêng"><em>I</em></button>
                                    <button type="button" data-wrap="__" title="Gạch chân"><u>U</u></button>
                                    <button type="button" data-wrap="~~" title="Gạch ngang"><s>S</s></button>
                                    <button type="button" data-wrap="||" title="Spoiler">◉</button>
                                    <button type="button" data-prefix="> " title="Trích dẫn"><i class="fa-solid fa-quote-left"></i></button>
                                    <button type="button" data-wrap="`" title="Mã inline"><i class="fa-solid fa-code"></i></button>
                                    <button type="button" data-prefix="- " title="Danh sách"><i class="fa-solid fa-list-ul"></i></button>
                                    <button type="button" data-wrap="[" data-suffix="](https://)" title="Chèn liên kết"><i class="fa-solid fa-link"></i></button>
                                </div>
                                <textarea id="announcement-content" name="content" rows="12"
                                    class="form-control aa-form-control aa-editor @error('content') is-invalid @enderror"
                                    maxlength="50000" placeholder="Soạn hoặc dán nội dung từ Discord...">{{ old('content') }}</textarea>
                                @error('content')<div class="aa-validation">{{ $message }}</div>@enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="aa-preview-status" aria-live="polite"></span>
                                    <span class="text-muted small"><span data-char-count>0</span> / 50.000</span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Bản xem trước</label>
                                    <span class="text-muted small">Gần giống giao diện Discord</span>
                                </div>
                                <div class="aa-preview" data-preview aria-live="polite">
                                    <div class="aa-preview-empty"><div><i class="fa-regular fa-eye fs-4 mb-2"></i><div>Nội dung đã định dạng sẽ hiển thị tại đây.</div></div></div>
                                </div>
                            </div>
                        </div>

                        <details class="aa-help mt-3">
                            <summary class="fw-bold">Cú pháp Discord được hỗ trợ</summary>
                            <div class="mt-2">
                                <code>**đậm**</code> · <code>*nghiêng*</code> · <code>__gạch chân__</code> ·
                                <code>~~gạch ngang~~</code> · <code>||spoiler||</code> · <code>&gt; trích dẫn</code> ·
                                <code>`mã`</code> · <code>- danh sách</code> · emoji Discord tùy chỉnh ·
                                <code>&lt;t:unix:F&gt;</code> (thời gian).
                            </div>
                        </details>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-warning fw-bold px-4" type="submit">
                                <i class="fa-solid fa-paper-plane me-2"></i>Đăng thông báo
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="aa-card">
                <div class="aa-section-head">
                    <div>
                        <h2><i class="fa-solid fa-box-archive me-2 text-warning"></i>Kho thông báo</h2>
                        <p>{{ number_format($announcementCount) }} bản ghi · trang {{ $announcements->currentPage() }} / {{ max(1, $announcements->lastPage()) }}</p>
                    </div>
                </div>

                @forelse ($announcements as $announcement)
                    <article class="aa-item">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <div class="aa-item-title-row">
                                    @if ($announcement->image_display_url)
                                        <img class="aa-item-thumb" src="{{ $announcement->image_display_url }}"
                                            alt="" loading="lazy" decoding="async">
                                    @endif
                                    <h3 class="aa-item-title">{{ $announcement->title }}</h3>
                                </div>
                                <div class="aa-item-meta">
                                    <i class="fa-regular fa-user me-1"></i>
                                    {{ $announcement->author?->employee?->name_ingame ?? $announcement->author?->username ?? 'Ban quản lý' }}
                                    <span class="mx-1">·</span>
                                    <time datetime="{{ $announcement->created_at->toIso8601String() }}">{{ $announcement->created_at->format('d/m/Y · H:i') }}</time>
                                </div>
                                <div class="aa-item-preview">{{ strip_tags($announcement->rendered_content) }}</div>
                                <a class="small fw-semibold text-decoration-none" href="{{ route('announcements.show', $announcement) }}" target="_blank">
                                    <i class="fa-regular fa-eye me-1"></i>Xem chi tiết
                                    @if (count($announcement->image_display_urls) > 1)
                                        · {{ count($announcement->image_display_urls) }} ảnh
                                    @endif
                                </a>
                            </div>
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-regular fa-trash-can me-1"></i>Xóa
                                </button>
                            </form>
                        </div>

                        <details class="mt-3">
                            <summary><i class="fa-solid fa-pen me-1"></i>Chỉnh sửa thông báo</summary>
                            <form class="mt-3" action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data"
                                data-announcement-editor data-preview-url="{{ route('admin.announcements.preview') }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="title-{{ $announcement->id }}" class="form-label fw-semibold">Tiêu đề</label>
                                    <input id="title-{{ $announcement->id }}" name="title" class="form-control aa-form-control"
                                        value="{{ $announcement->title }}" maxlength="255" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ảnh minh họa <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                                    <div class="aa-image-panel">
                                        <div class="aa-image-choice">
                                            <label for="image-url-{{ $announcement->id }}" class="form-label small fw-semibold">Dán liên kết ảnh để thay ảnh hiện tại</label>
                                            <input id="image-url-{{ $announcement->id }}" name="image_url" type="url" inputmode="url"
                                                class="form-control aa-form-control mb-2 @error('image_url') is-invalid @enderror"
                                                value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                                            @error('image_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <div class="text-center text-muted small my-2">hoặc</div>
                                            <label for="image-files-{{ $announcement->id }}" class="form-label small fw-semibold">Tải ảnh mới từ thiết bị (thay toàn bộ ảnh hiện tại)</label>
                                            <input id="image-files-{{ $announcement->id }}" name="image_files[]" type="file" multiple
                                                class="form-control aa-form-control @error('image_files') is-invalid @enderror"
                                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                            @error('image_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @error('image_files')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @error('image_files.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <div class="aa-image-hint mt-2">JPG, PNG hoặc WebP · tối đa 5 MB mỗi ảnh, tối đa 10 ảnh. Để nguyên nếu không muốn thay đổi.</div>
                                            <div class="aa-image-hint" data-image-count aria-live="polite"></div>
                                            @if (count($announcement->image_display_urls) > 1)
                                                <div class="d-flex flex-wrap gap-2 mt-2" aria-label="Ảnh hiện tại">
                                                    @foreach ($announcement->image_display_urls as $imageUrl)
                                                        <img class="aa-item-thumb" src="{{ $imageUrl }}" alt="" loading="lazy">
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_image" value="1"
                                                    id="remove-image-{{ $announcement->id }}">
                                                <label class="form-check-label small" for="remove-image-{{ $announcement->id }}">Xóa ảnh hiện tại</label>
                                            </div>
                                            <div class="aa-image-error mt-2" data-image-error role="alert"></div>
                                        </div>
                                        <div class="aa-image-preview is-visible"
                                            data-image-preview data-current-image="{{ $announcement->image_display_url }}">
                                            <div class="aa-image-placeholder" @if ($announcement->image_display_url) hidden @endif>
                                                <div><i class="fa-regular fa-image fs-3 mb-2"></i><div>Chưa có ảnh minh họa</div></div>
                                            </div>
                                            <img alt="Xem trước ảnh thông báo" data-image-element
                                                src="{{ $announcement->image_display_url }}"
                                                @if (! $announcement->image_display_url) hidden @endif
                                                onerror="this.hidden=true;this.closest('[data-image-preview]').classList.remove('is-visible')">
                                        </div>
                                    </div>
                                </div>
                                <div class="aa-editor-grid">
                                    <div>
                                        <label for="content-{{ $announcement->id }}" class="form-label fw-semibold">Nội dung</label>
                                        <div class="aa-toolbar" role="toolbar" aria-label="Định dạng nội dung">
                                            <button type="button" data-wrap="**" title="In đậm"><strong>B</strong></button>
                                            <button type="button" data-wrap="*" title="In nghiêng"><em>I</em></button>
                                            <button type="button" data-wrap="__" title="Gạch chân"><u>U</u></button>
                                            <button type="button" data-wrap="~~" title="Gạch ngang"><s>S</s></button>
                                            <button type="button" data-wrap="||" title="Spoiler">◉</button>
                                            <button type="button" data-prefix="> " title="Trích dẫn"><i class="fa-solid fa-quote-left"></i></button>
                                            <button type="button" data-wrap="`" title="Mã inline"><i class="fa-solid fa-code"></i></button>
                                            <button type="button" data-prefix="- " title="Danh sách"><i class="fa-solid fa-list-ul"></i></button>
                                            <button type="button" data-wrap="[" data-suffix="](https://)" title="Chèn liên kết"><i class="fa-solid fa-link"></i></button>
                                        </div>
                                        <textarea id="content-{{ $announcement->id }}" name="content" rows="9"
                                            class="form-control aa-form-control aa-editor" maxlength="50000" required>{{ $announcement->content }}</textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <span class="aa-preview-status" aria-live="polite"></span>
                                            <span class="text-muted small"><span data-char-count>{{ mb_strlen($announcement->content) }}</span> / 50.000</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-semibold mb-0">Bản xem trước</label>
                                            <span class="text-muted small">Discord Markdown</span>
                                        </div>
                                        <div class="aa-preview" data-preview>
                                            <div class="aa-preview-empty"><div><i class="fa-solid fa-spinner fa-spin fs-4 mb-2"></i><div>Đang tải bản xem trước...</div></div></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </details>
                    </article>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fa-regular fa-folder-open fs-2 d-block mb-3"></i>
                        Chưa có thông báo nào. Hãy tạo thông báo đầu tiên ở phía trên.
                    </div>
                @endforelse

                @if ($announcements->hasPages())
                    <div class="p-3 border-top">{{ $announcements->links() }}</div>
                @endif
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-announcement-editor]').forEach(form => {
            const textarea = form.querySelector('textarea[name="content"]');
            const preview = form.querySelector('[data-preview]');
            const status = form.querySelector('.aa-preview-status');
            const counter = form.querySelector('[data-char-count]');
            const imageUrlInput = form.querySelector('input[name="image_url"]');
            const imageFileInput = form.querySelector('input[name="image_files[]"]');
            const imagePreview = form.querySelector('[data-image-preview]');
            const imageElement = imagePreview?.querySelector('[data-image-element]');
            const imagePlaceholder = imagePreview?.querySelector('.aa-image-placeholder');
            const imageError = form.querySelector('[data-image-error]');
            const removeImageInput = form.querySelector('input[name="remove_image"]');
            let timer;
            let imageObjectUrl;

            const setImagePreview = source => {
                if (!source || !imagePreview || !imageElement) {
                    if (imagePreview) imagePreview.classList.remove('is-visible');
                    if (imageElement) imageElement.hidden = true;
                    if (imagePlaceholder) imagePlaceholder.hidden = false;
                    return;
                }

                imageElement.hidden = false;
                imagePreview.classList.add('is-visible');
                if (imagePlaceholder) imagePlaceholder.hidden = true;
                imageElement.src = source;
            };

            imageUrlInput?.addEventListener('input', () => {
                if (imageFileInput) imageFileInput.value = '';
                const countLabel = form.querySelector('[data-image-count]');
                if (countLabel) countLabel.textContent = '';
                if (imageObjectUrl) {
                    URL.revokeObjectURL(imageObjectUrl);
                    imageObjectUrl = null;
                }
                if (removeImageInput) removeImageInput.checked = false;
                imageError.textContent = '';
                imageError.style.display = 'none';

                const value = imageUrlInput.value.trim();
                if (!value) {
                    setImagePreview(imagePreview.dataset.currentImage || '');
                    return;
                }

                try {
                    const parsed = new URL(value);
                    if (!['http:', 'https:'].includes(parsed.protocol)) throw new Error();
                    setImagePreview(parsed.href);
                } catch {
                    setImagePreview('');
                    imageError.textContent = 'Liên kết ảnh phải bắt đầu bằng http:// hoặc https://.';
                    imageError.style.display = 'block';
                }
            });

            imageFileInput?.addEventListener('change', () => {
                if (imageUrlInput) imageUrlInput.value = '';
                if (removeImageInput) removeImageInput.checked = false;
                imageError.textContent = '';
                imageError.style.display = 'none';
                if (imageObjectUrl) URL.revokeObjectURL(imageObjectUrl);

                const files = Array.from(imageFileInput.files || []);
                const countLabel = form.querySelector('[data-image-count]');
                if (countLabel) countLabel.textContent = files.length ? `Đã chọn ${files.length} ảnh.` : '';
                const file = files[0];
                if (!file) {
                    setImagePreview(imagePreview.dataset.currentImage || '');
                    return;
                }
                if (files.length > 10 || files.some(selected =>
                    !['image/jpeg', 'image/png', 'image/webp'].includes(selected.type) || selected.size > 5 * 1024 * 1024
                )) {
                    imageFileInput.value = '';
                    if (countLabel) countLabel.textContent = '';
                    setImagePreview(imagePreview.dataset.currentImage || '');
                    imageError.textContent = 'Chọn tối đa 10 ảnh JPG, PNG hoặc WebP, mỗi ảnh tối đa 5 MB.';
                    imageError.style.display = 'block';
                    return;
                }
                imageObjectUrl = URL.createObjectURL(file);
                setImagePreview(imageObjectUrl);
            });

            removeImageInput?.addEventListener('change', () => {
                if (removeImageInput.checked) {
                    if (imageUrlInput) imageUrlInput.value = '';
                    if (imageFileInput) imageFileInput.value = '';
                    if (imageObjectUrl) {
                        URL.revokeObjectURL(imageObjectUrl);
                        imageObjectUrl = null;
                    }
                    setImagePreview('');
                } else {
                    setImagePreview(imagePreview.dataset.currentImage || '');
                }
            });

            const renderPreview = async () => {
                if (!textarea.value.trim()) {
                    preview.innerHTML = '<div class="aa-preview-empty"><div><i class="fa-regular fa-eye fs-4 mb-2"></i><div>Nội dung đã định dạng sẽ hiển thị tại đây.</div></div></div>';
                    status.textContent = '';
                    return;
                }

                status.textContent = 'Đang cập nhật bản xem trước...';
                try {
                    const response = await fetch(form.dataset.previewUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ content: textarea.value })
                    });
                    if (!response.ok) throw new Error('Không thể tải bản xem trước (' + response.status + ').');
                    const result = await response.json();
                    preview.innerHTML = result.html;
                    status.textContent = 'Bản xem trước đã cập nhật';
                } catch (error) {
                    status.textContent = error.message || 'Không thể tạo bản xem trước. Vui lòng thử lại.';
                }
            };

            textarea.addEventListener('input', () => {
                counter.textContent = textarea.value.length.toLocaleString('vi-VN');
                clearTimeout(timer);
                timer = setTimeout(renderPreview, 300);
            });

            form.querySelectorAll('[data-wrap], [data-prefix]').forEach(button => {
                button.addEventListener('click', () => {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const selection = textarea.value.slice(start, end);
                    let replacement;

                    if (button.dataset.prefix) {
                        const lines = (selection || 'Nội dung').split('\n').map(line => button.dataset.prefix + line);
                        replacement = lines.join('\n');
                    } else {
                        const before = button.dataset.wrap;
                        const after = button.dataset.suffix || before;
                        replacement = before + (selection || 'nội dung') + after;
                    }

                    textarea.setRangeText(replacement, start, end, 'select');
                    textarea.focus();
                    textarea.dispatchEvent(new Event('input'));
                });
            });

            renderPreview();
        });

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
