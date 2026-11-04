<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementComment;
use App\Models\User;
use App\Notifications\AnnouncementCommentActivity;
use App\Support\DiscordMarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementController extends Controller
{
    private const REACTION_TYPES = [
        'like' => ['label' => 'Thích', 'emoji' => '👍'],
        'love' => ['label' => 'Yêu thích', 'emoji' => '❤️'],
        'care' => ['label' => 'Thương thương', 'emoji' => '🥰'],
        'haha' => ['label' => 'Haha', 'emoji' => '😂'],
        'wow' => ['label' => 'Wow', 'emoji' => '😮'],
        'sad' => ['label' => 'Buồn', 'emoji' => '😢'],
        'angry' => ['label' => 'Phẫn nộ', 'emoji' => '😡'],
    ];

    public function index(Request $request, DiscordMarkdownRenderer $renderer): View
    {
        $search = trim((string) $request->query('q', ''));
        $announcements = Announcement::with('author.employee')
            ->withCount(['reactions', 'comments'])
            ->when($search !== '', fn ($query) => $query
                ->where('title', 'like', '%'.$search.'%')
                ->orWhere('content', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $announcements->getCollection()->each(
            fn (Announcement $announcement) => $announcement->setAttribute(
                'rendered_content',
                $renderer->render($announcement->content)
            )
        );

        return view('announcements.index', compact('announcements', 'search'));
    }

    public function show(Request $request, Announcement $announcement, DiscordMarkdownRenderer $renderer): View
    {
        $announcement->load('author.employee')->loadCount(['reactions', 'comments']);
        $announcement->setAttribute('rendered_content', $renderer->render($announcement->content));
        $reactionCounts = $this->reactionCounts($announcement);
        $comments = $this->commentThreads($announcement);
        if ($request->filled('focus_comment')) {
            $focusComment = AnnouncementComment::query()
                ->where('announcement_id', $announcement->id)
                ->findOrFail((int) $request->query('focus_comment'));
            while ($focusComment->parent_id) {
                $focusComment = $focusComment->parent()->firstOrFail();
            }
            if (! $comments->getCollection()->contains('id', $focusComment->id)) {
                $this->loadCommentReplies(collect([$focusComment]));
                $comments->getCollection()->push($focusComment);
            }
        }
        $currentReaction = auth()->user()
            ? $announcement->reactions()->where('user_id', auth()->id())->value('type')
            : null;

        return view('announcements.show', [
            'announcement' => $announcement,
            'comments' => $comments,
            'currentReaction' => $currentReaction,
            'reactionCounts' => $reactionCounts,
            'reactionTypes' => self::REACTION_TYPES,
        ]);
    }

    public function interactionState(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $request->validate([
            'comments_limit' => ['sometimes', 'integer', Rule::in([3, 5])],
            'comments_cursor' => ['sometimes', 'nullable', 'string', 'max:1024'],
        ]);

        return response()->json($this->interactionData(
            $request,
            $announcement,
            $data['comments_limit'] ?? 3
        ));
    }

    public function interactionSummaries(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:5'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:announcements,id'],
        ]);
        $announcements = Announcement::query()
            ->whereIn('id', $data['ids'])
            ->withCount(['reactions', 'comments'])
            ->get();

        return response()->json($announcements->map(fn (Announcement $announcement) => [
            'id' => $announcement->id,
            'reactions_count' => $announcement->reactions_count,
            'comments_count' => $announcement->comments_count,
        ]));
    }

    public function toggleReaction(Request $request, Announcement $announcement): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(self::REACTION_TYPES))],
        ]);

        $reaction = $announcement->reactions()->where('user_id', $request->user()->id)->first();

        if ($reaction?->type === $data['type']) {
            $reaction->delete();
        } else {
            $announcement->reactions()->updateOrCreate(
                ['user_id' => $request->user()->id],
                ['type' => $data['type']]
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã cập nhật cảm xúc.']);
        }

        return redirect()->to(route('announcements.show', $announcement).'#reactions');
    }

    public function storeComment(Request $request, Announcement $announcement): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $announcement->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);
        $this->notifyCommentRecipient($request, $announcement, $comment, false);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã đăng bình luận.'], 201);
        }

        return redirect()->to(route('announcements.show', $announcement).'#comments')
            ->with('success', 'Đã đăng bình luận.');
    }

    public function storeReply(
        Request $request,
        Announcement $announcement,
        AnnouncementComment $comment
    ): JsonResponse|RedirectResponse {
        abort_unless($comment->announcement_id === $announcement->id, 404);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $reply = $announcement->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $comment->id,
            'body' => $data['body'],
        ]);
        $this->notifyCommentRecipient($request, $announcement, $reply, true, $comment->user);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã đăng phản hồi.'], 201);
        }

        return redirect()->to(route('announcements.show', $announcement).'#comment-'.$comment->id);
    }

    public function destroyComment(
        Request $request,
        Announcement $announcement,
        AnnouncementComment $comment
    ): JsonResponse|RedirectResponse {
        abort_unless($comment->announcement_id === $announcement->id, 404);
        abort_unless(
            $request->user()->id === $comment->user_id || $request->user()->isDownAdminRole(),
            403
        );

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã xóa bình luận.']);
        }

        return redirect()->to(route('announcements.show', $announcement).'#comments')
            ->with('success', 'Đã xóa bình luận.');
    }

    private function reactionCounts(Announcement $announcement): Collection
    {
        return $announcement->reactions()
            ->selectRaw('type, COUNT(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');
    }

    private function commentThreads(Announcement $announcement, int $limit = 3, ?string $cursor = null)
    {
        $comments = $announcement->comments()
            ->whereNull('parent_id')
            ->with('user.employee')
            ->orderByDesc('id')
            ->cursorPaginate($limit, ['*'], 'comments_cursor', $cursor);
        $this->loadCommentReplies($comments->getCollection());

        return $comments;
    }

    private function loadCommentReplies(Collection $parents): void
    {
        $frontier = $parents;

        while ($frontier->isNotEmpty()) {
            $replies = AnnouncementComment::query()
                ->whereIn('parent_id', $frontier->pluck('id'))
                ->with('user.employee')
                ->orderBy('id')
                ->get()
                ->groupBy('parent_id');
            $nextFrontier = collect();

            foreach ($frontier as $parent) {
                $children = $replies->get($parent->id, collect());
                $parent->setRelation('replies', $children);
                $nextFrontier = $nextFrontier->concat($children);
            }

            $frontier = $nextFrontier;
        }
    }

    private function interactionData(Request $request, Announcement $announcement, int $commentsLimit): array
    {
        $comments = $this->commentThreads(
            $announcement,
            $commentsLimit,
            $data['comments_cursor'] ?? null
        );
        $reactionCounts = $this->reactionCounts($announcement);

        return [
            'reactions' => $reactionCounts,
            'reactions_count' => $reactionCounts->sum(),
            'comments_count' => $announcement->comments()->count(),
            'current_reaction' => $request->user()
                ? $announcement->reactions()->where('user_id', $request->user()->id)->value('type')
                : null,
            'comments_next_cursor' => $comments->nextCursor()?->encode(),
            'comments_html' => view('announcements.partials.comments', [
                'announcement' => $announcement,
                'comments' => $comments,
            ])->render(),
        ];
    }

    private function notifyCommentRecipient(
        Request $request,
        Announcement $announcement,
        AnnouncementComment $comment,
        bool $isReply,
        ?User $replyRecipient = null
    ): void {
        $recipient = $isReply ? $replyRecipient : $announcement->author;

        if (! $recipient || $recipient->is($request->user())) {
            return;
        }

        $actor = $request->user();
        $recipient->notify(new AnnouncementCommentActivity(
            $announcement->id,
            $comment->id,
            $actor->id,
            $actor->employee?->name_ingame ?? $actor->username,
            $comment->body,
            $isReply,
        ));
    }

    public function manage(DiscordMarkdownRenderer $renderer): View
    {
        $announcements = Announcement::with('author.employee')
            ->latest()
            ->paginate(20);
        $announcements->getCollection()->each(
            fn (Announcement $announcement) => $announcement->setAttribute(
                'rendered_content',
                $renderer->render($announcement->content)
            )
        );
        $announcementCount = Announcement::count();
        $latestAnnouncement = Announcement::latest()->value('created_at');

        return view('announcements.manage', compact('announcements', 'announcementCount', 'latestAnnouncement'));
    }

    public function preview(Request $request, DiscordMarkdownRenderer $renderer): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:50000'],
        ]);

        return response()->json(['html' => $renderer->render($data['content'])]);
    }

    public function image(Announcement $announcement, ?int $imageIndex = 0): StreamedResponse
    {
        $imagePath = $announcement->storedImagePaths()[$imageIndex] ?? null;
        abort_unless($imagePath, 404);
        abort_unless(Storage::disk('public')->exists($imagePath), 404);

        return Storage::disk('public')->response($imagePath, null, [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $unread = $this->unreadForUser($request->user()->id);
        $announcements = (clone $unread)
            ->with('author.employee')
            ->latest()
            ->limit(8)
            ->get();
        $commentNotifications = $request->user()->unreadNotifications()
            ->where('type', AnnouncementCommentActivity::class)
            ->latest()
            ->limit(8)
            ->get();
        $items = $announcements->map(fn (Announcement $announcement) => [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'excerpt' => Str::limit(strip_tags($announcement->content), 110),
            'author' => $announcement->author?->employee?->name_ingame
                ?? $announcement->author?->username
                ?? 'Ban quản lý',
            'time' => $announcement->created_at->diffForHumans(),
            'url' => route('announcements.read', $announcement),
            'method' => 'POST',
            'timestamp' => $announcement->created_at->timestamp,
        ])->concat($commentNotifications->map(function ($notification) {
            $data = $notification->data;

            return [
                'id' => $notification->id,
                'title' => ($data['actor_name'] ?? 'Một thành viên')
                    .(filled($data['is_reply'] ?? false) ? ' đã phản hồi bình luận' : ' đã bình luận bài viết'),
                'excerpt' => Str::limit($data['body'] ?? '', 110),
                'author' => 'Tương tác bài viết',
                'time' => $notification->created_at->diffForHumans(),
                'url' => route('announcements.comment-notifications.open', $notification->id),
                'method' => 'GET',
                'timestamp' => $notification->created_at->timestamp,
            ];
        }))->sortByDesc('timestamp')->take(8)->values()
            ->map(fn (array $item) => collect($item)->except('timestamp')->all());

        return response()->json([
            'count' => $unread->count() + $request->user()->unreadNotifications()
                ->where('type', AnnouncementCommentActivity::class)
                ->count(),
            'items' => $items,
        ]);
    }

    public function openCommentNotification(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()->notifications()
            ->whereKey($notification)
            ->where('type', AnnouncementCommentActivity::class)
            ->firstOrFail();
        $data = $notification->data;
        $notification->markAsRead();

        $announcement = Announcement::findOrFail($data['announcement_id']);

        return redirect()->to(route('announcements.show', [
            'announcement' => $announcement,
            'focus_comment' => $data['comment_id'],
        ]).'#comment-'.$data['comment_id']);
    }

    public function markAsRead(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->readByUsers()->syncWithoutDetaching([
            $request->user()->id => ['read_at' => now()],
        ]);

        return redirect()->route('announcements.show', $announcement);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedAnnouncement($request);
        $imagePaths = $this->storeImages($request);

        try {
            $request->user()->announcements()->create([
                'title' => $data['title'],
                'content' => $data['content'],
                'image_url' => $data['image_url'] ?? null,
                'image_path' => $imagePaths[0] ?? null,
                'image_paths' => $imagePaths ?: null,
            ]);
        } catch (\Throwable $exception) {
            $this->deleteStoredImages($imagePaths);

            throw $exception;
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đã đăng thông báo.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->validatedAnnouncement($request);
        $oldImagePaths = $announcement->storedImagePaths();
        $newImagePaths = $this->storeImages($request);
        $imageFields = [];

        if ($newImagePaths !== []) {
            $imageFields = [
                'image_url' => null,
                'image_path' => $newImagePaths[0],
                'image_paths' => $newImagePaths,
            ];
        } elseif (filled($data['image_url'] ?? null)) {
            $imageFields = ['image_url' => $data['image_url'], 'image_path' => null, 'image_paths' => null];
        } elseif ($request->boolean('remove_image')) {
            $imageFields = ['image_url' => null, 'image_path' => null, 'image_paths' => null];
        }

        try {
            $announcement->update([
                'title' => $data['title'],
                'content' => $data['content'],
                ...$imageFields,
            ]);
        } catch (\Throwable $exception) {
            $this->deleteStoredImages($newImagePaths);

            throw $exception;
        }

        if ($imageFields !== []) {
            $this->deleteStoredImages($oldImagePaths);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đã cập nhật thông báo.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $imagePaths = $announcement->storedImagePaths();
        $announcement->delete();

        $this->deleteStoredImages($imagePaths);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đã xóa thông báo.');
    }

    private function validatedAnnouncement(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'image_url' => ['nullable', 'string', 'max:2048', 'url', 'regex:/^https?:\/\//i'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'image_files' => ['nullable', 'array', 'max:10'],
            'image_files.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $fileCount = count((array) $request->file('image_files', [])) + (int) $request->hasFile('image_file');

        if ($fileCount > 10) {
            throw ValidationException::withMessages([
                'image_files' => 'Mỗi thông báo chỉ được tải lên tối đa 10 ảnh.',
            ]);
        }

        if ($fileCount > 0 && filled($data['image_url'] ?? null)) {
            throw ValidationException::withMessages([
                'image_files' => 'Vui lòng chọn URL hoặc tải ảnh lên, không dùng đồng thời cả hai.',
            ]);
        }

        return $data;
    }

    private function storeImages(Request $request): array
    {
        $files = array_values(array_filter((array) $request->file('image_files', [])));
        if ($request->hasFile('image_file')) {
            $files[] = $request->file('image_file');
        }

        $paths = [];
        foreach ($files as $file) {
            $path = $file->store('announcements', 'public');

            if (! $path) {
                $this->deleteStoredImages($paths);
                throw new \RuntimeException('Không thể lưu ảnh thông báo vào ổ đĩa public.');
            }

            $paths[] = $path;
        }

        return $paths;
    }

    private function deleteStoredImages(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
                throw new \RuntimeException('Không thể xóa ảnh thông báo: '.$path);
            }
        }
    }

    private function unreadForUser(int $userId): Builder
    {
        return Announcement::query()
            ->whereDoesntHave('readByUsers', fn ($query) => $query->whereKey($userId));
    }
}
