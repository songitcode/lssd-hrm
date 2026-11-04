<article class="ad-comment" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
    <div class="ad-comment-card">
        <div class="ad-comment-head">
            <span class="ad-comment-avatar" aria-hidden="true">
                {{ mb_strtoupper(mb_substr($comment->user?->employee?->name_ingame ?? $comment->user?->username ?? 'T', 0, 1)) }}
            </span>
            <div class="ad-comment-identity">
                <strong>{{ $comment->user?->employee?->name_ingame ?? $comment->user?->username ?? 'Tài khoản đã xóa' }}</strong>
                <time datetime="{{ $comment->created_at->toIso8601String() }}">
                    {{ $comment->created_at->diffForHumans() }}
                </time>
            </div>
        </div>
        @php
            $commentPreviewLength = 320;
            $commentIsLong = mb_strlen($comment->body) > $commentPreviewLength;
        @endphp
        <div class="ad-comment-body {{ $commentIsLong ? 'is-collapsed' : '' }}" data-comment-text>
            <p data-comment-preview>{{ $commentIsLong ? mb_substr($comment->body, 0, $commentPreviewLength).'…' : $comment->body }}</p>
            @if ($commentIsLong)
                <p class="d-none" data-comment-full>{{ $comment->body }}</p>
                <button class="ad-comment-expand" type="button" data-comment-expand aria-expanded="false">Xem thêm</button>
            @endif
        </div>
        <div class="ad-comment-actions">
            @auth
                <button class="ad-comment-action" type="button" data-reply-toggle="{{ $comment->id }}">
                    <i class="fa-solid fa-reply me-1"></i>Trả lời
                </button>
                @if (auth()->id() === $comment->user_id || auth()->user()->isDownAdminRole())
                    <form class="ad-inline-form" action="{{ route('announcements.comments.destroy', [$announcement, $comment]) }}"
                        method="POST" data-interaction-form>
                        @csrf
                        @method('DELETE')
                        <button class="ad-comment-action ad-comment-delete" type="submit">
                            <i class="fa-regular fa-trash-can me-1"></i>Xóa
                        </button>
                    </form>
                @endif
            @endauth
        </div>
        @auth
            <form class="ad-reply-form d-none" id="reply-form-{{ $comment->id }}"
                action="{{ route('announcements.comments.reply', [$announcement, $comment]) }}" method="POST" data-interaction-form>
                @csrf
                <label class="visually-hidden" for="reply-body-{{ $comment->id }}">Trả lời bình luận</label>
                <textarea class="form-control form-control-sm" id="reply-body-{{ $comment->id }}" name="body"
                    rows="2" maxlength="2000" required placeholder="Viết phản hồi..."></textarea>
                <div class="ad-reply-submit">
                    <span>Enter để gửi · Tối đa 2.000 ký tự</span>
                    <button class="btn btn-primary btn-sm" type="submit">Gửi</button>
                </div>
            </form>
        @endauth
    </div>

    @if ($comment->replies->isNotEmpty())
        <div class="ad-replies">
            @foreach ($comment->replies as $reply)
                @include('announcements.partials.comment', ['comment' => $reply, 'announcement' => $announcement])
            @endforeach
        </div>
    @endif
</article>
