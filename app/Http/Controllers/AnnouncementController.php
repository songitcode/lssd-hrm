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

    public function image(Announcement $announcement): StreamedResponse
    {
        abort_unless($announcement->image_path, 404);
        abort_unless(Storage::disk('public')->exists($announcement->image_path), 404);

        return Storage::disk('public')->response($announcement->image_path, null, [
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

        return redirect()->to(route('announcements.index').'#announcement-'.$announcement->id);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedAnnouncement($request);
        $imagePath = $this->storeImage($request);

        try {
            $request->user()->announcements()->create([
                'title' => $data['title'],
                'content' => $data['content'],
                'image_url' => $data['image_url'] ?? null,
                'image_path' => $imagePath,
            ]);
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đã đăng thông báo.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->validatedAnnouncement($request);
        $oldImagePath = $announcement->image_path;
        $newImagePath = $this->storeImage($request);
        $imageFields = [];

        if ($newImagePath) {
            $imageFields = ['image_url' => null, 'image_path' => $newImagePath];
        } elseif (filled($data['image_url'] ?? null)) {
            $imageFields = ['image_url' => $data['image_url'], 'image_path' => null];
        } elseif ($request->boolean('remove_image')) {
            $imageFields = ['image_url' => null, 'image_path' => null];
        }

        try {
            $announcement->update([
                'title' => $data['title'],
                'content' => $data['content'],
                ...$imageFields,
            ]);
        } catch (\Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($oldImagePath && $oldImagePath !== $newImagePath && $imageFields !== []) {
            $this->deleteStoredImage($oldImagePath);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đã cập nhật thông báo.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $imagePath = $announcement->image_path;
        $announcement->delete();

        if ($imagePath) {
            $this->deleteStoredImage($imagePath);
        }

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
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image_file') && filled($data['image_url'] ?? null)) {
            throw ValidationException::withMessages([
                'image_file' => 'Vui lòng chọn URL hoặc tải ảnh lên, không dùng đồng thời cả hai.',
            ]);
        }

        return $data;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image_file')) {
            return null;
        }

        $path = $request->file('image_file')->store('announcements', 'public');

        if (! $path) {
            throw new \RuntimeException('Không thể lưu ảnh thông báo vào ổ đĩa public.');
        }

        return $path;
    }

    private function deleteStoredImage(string $path): void
    {
        if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
            throw new \RuntimeException('Không thể xóa ảnh thông báo: '.$path);
        }
    }

    private function unreadForUser(int $userId): Builder
    {
        return Announcement::query()
            ->whereDoesntHave('readByUsers', fn ($query) => $query->whereKey($userId));
    }
}
