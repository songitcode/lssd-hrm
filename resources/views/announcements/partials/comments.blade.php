<div id="ad-comment-content">
    @if ($comments->isEmpty() && ! $comments->previousCursor())
        <p class="ad-comment-empty">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ ý kiến.</p>
    @else
        <div class="ad-comment-list">
            <div class="ad-comment-batch">
                @foreach ($comments as $comment)
                    @include('announcements.partials.comment', ['comment' => $comment, 'announcement' => $announcement])
                @endforeach
            </div>
        </div>
    @endif

    @if ($comments->hasMorePages())
        <button class="ad-load-comments" type="button"
            data-comments-cursor="{{ $comments->nextCursor()->encode() }}"
            data-comments-url="{{ route('announcements.interactions.state', $announcement) }}">
            <i class="fa-solid fa-chevron-down me-2"></i>Xem thêm 5 bình luận
        </button>
    @endif
</div>
