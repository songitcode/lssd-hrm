<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Support\DiscordMarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementController extends Controller
{
    public function index(Request $request, DiscordMarkdownRenderer $renderer): View
    {
        $search = trim((string) $request->query('q', ''));
        $announcements = Announcement::with('author.employee')
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

    public function show(Announcement $announcement, DiscordMarkdownRenderer $renderer): View
    {
        $announcement->load('author.employee');
        $announcement->setAttribute('rendered_content', $renderer->render($announcement->content));

        return view('announcements.show', compact('announcement'));
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

        return response()->json([
            'count' => $unread->count(),
            'items' => $announcements->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'excerpt' => Str::limit(strip_tags($announcement->content), 110),
                'author' => $announcement->author?->employee?->name_ingame
                    ?? $announcement->author?->username
                    ?? 'Ban quản lý',
                'time' => $announcement->created_at->diffForHumans(),
                'url' => route('announcements.read', $announcement),
            ]),
        ]);
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
