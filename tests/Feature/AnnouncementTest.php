<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use App\Support\DiscordMarkdownRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcements_are_visible_on_the_separate_public_page(): void
    {
        Announcement::create([
            'title' => 'Lịch họp',
            'content' => 'Cuộc họp bắt đầu lúc 09:00.',
        ]);

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Lịch họp')
            ->assertSee('Cuộc họp bắt đầu lúc 09:00.');
    }

    public function test_secretary_can_create_update_and_delete_announcements(): void
    {
        $secretary = $this->createUser('thư ký');
        $csrfToken = 'announcement-test-token';
        $this->actingAs($secretary);
        $this->withSession(['_token' => $csrfToken]);

        $this->get(route('admin.announcements.index'))->assertOk();

        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Thông báo mới',
            'content' => 'Nội dung thông báo.',
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::firstOrFail();
        $this->assertSame($secretary->id, $announcement->created_by);

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => 'Thông báo đã sửa',
            'content' => 'Nội dung đã cập nhật.',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Thông báo đã sửa',
            'content' => 'Nội dung đã cập nhật.',
        ]);
        $this->get(route('admin.announcements.index'))
            ->assertOk()
            ->assertSee('Thông báo đã sửa')
            ->assertSee('Nội dung đã cập nhật.');

        $this->delete(route('admin.announcements.destroy', $announcement), ['_token' => $csrfToken])
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_users_below_secretary_cannot_manage_announcements(): void
    {
        $csrfToken = 'announcement-test-token';
        $this->actingAs($this->createUser('cảnh sát viên'));
        $this->withSession(['_token' => $csrfToken]);

        $this->get(route('admin.announcements.index'))->assertForbidden();
        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Không được phép',
            'content' => 'Không được phép đăng.',
        ])->assertForbidden();
    }

    public function test_discord_markdown_renders_formatting_and_escapes_raw_html(): void
    {
        $html = app(DiscordMarkdownRenderer::class)->render(
            "**đậm** __gạch chân__ ~~xóa~~ ||bí mật||\n> trích dẫn\n- mục\n`mã`\n\n<script>alert(1)</script>"
        );

        $this->assertStringContainsString('<strong>đậm</strong>', $html);
        $this->assertStringContainsString('<u>gạch chân</u>', $html);
        $this->assertStringContainsString('<del>xóa</del>', $html);
        $this->assertStringContainsString('class="discord-spoiler"', $html);
        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringContainsString('<li>mục', $html);
        $this->assertStringContainsString('<code>mã</code>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);

        $codeBlock = app(DiscordMarkdownRenderer::class)->render("```php\n__not underline__\n```");
        $this->assertStringContainsString('<pre><code class="language-php">__not underline__</code></pre>', $codeBlock);
        $this->assertStringNotContainsString('<p><pre>', $codeBlock);
    }

    public function test_discord_custom_emoji_and_timestamp_are_displayed(): void
    {
        $html = app(DiscordMarkdownRenderer::class)->render(
            '<:sheriff:12345678901234567> <t:0:F>'
        );

        $this->assertStringContainsString('cdn.discordapp.com/emojis/12345678901234567.webp', $html);
        $this->assertStringContainsString('class="discord-timestamp"', $html);
        $this->assertStringContainsString('01 January 1970 08:00', $html);
    }

    public function test_announcement_preview_endpoint_returns_safe_discord_markdown(): void
    {
        $csrfToken = 'announcement-preview-token';
        $this->actingAs($this->createUser('thư ký'));
        $this->withSession(['_token' => $csrfToken]);

        $this->postJson(route('admin.announcements.preview'), [
            '_token' => $csrfToken,
            'content' => '**Bản xem trước** <img src=x onerror=alert(1)>',
        ])
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, '<strong>Bản xem trước</strong>')
                && ! str_contains($html, '<img src=x')
                && str_contains($html, '&lt;img')
            );
    }

    public function test_secretary_can_upload_replace_and_remove_announcement_image(): void
    {
        Storage::fake('public');
        $csrfToken = 'announcement-image-token';
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
        $this->actingAs($this->createUser('thư ký'));
        $this->withSession(['_token' => $csrfToken]);

        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Thông báo có ảnh',
            'content' => 'Nội dung có ảnh.',
            'image_file' => UploadedFile::fake()->createWithContent('notice.png', $pngContent),
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::firstOrFail();
        $uploadedPath = $announcement->image_path;
        $this->assertNotNull($uploadedPath);
        Storage::disk('public')->assertExists($uploadedPath);
        $imageUrl = route('announcements.image', $announcement, false);
        config(['app.url' => 'https://incorrect.example']);

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee($imageUrl);
        $this->get(route('admin.announcements.index'))
            ->assertOk()
            ->assertSee($imageUrl)
            ->assertSee('Dán liên kết ảnh để thay ảnh hiện tại');
        $this->get($imageUrl)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertStreamedContent($pngContent);

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'image_file' => UploadedFile::fake()->createWithContent('replacement.png', $pngContent),
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement->refresh();
        $replacementPath = $announcement->image_path;
        $this->assertNotSame($uploadedPath, $replacementPath);
        Storage::disk('public')->assertMissing($uploadedPath);
        Storage::disk('public')->assertExists($replacementPath);

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'image_url' => 'https://example.com/notice.jpg',
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement->refresh();
        $this->assertSame('https://example.com/notice.jpg', $announcement->image_url);
        $this->assertNull($announcement->image_path);
        Storage::disk('public')->assertMissing($replacementPath);

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'remove_image' => '1',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertNull($announcement->fresh()->image_url);
        $this->assertNull($announcement->fresh()->image_path);
    }

    public function test_invalid_announcement_images_are_rejected(): void
    {
        $csrfToken = 'announcement-invalid-image-token';
        $this->actingAs($this->createUser('thư ký'));
        $this->withSession(['_token' => $csrfToken]);

        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Ảnh không hợp lệ',
            'content' => 'Nội dung.',
            'image_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('image_url');

        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Ảnh không hợp lệ',
            'content' => 'Nội dung.',
            'image_file' => UploadedFile::fake()->create('payload.svg', 20, 'image/svg+xml'),
        ])->assertSessionHasErrors('image_file');
    }

    public function test_staff_receive_unread_announcement_bell_and_can_open_notifications(): void
    {
        $recipient = $this->createUser('cảnh sát viên');
        $author = $this->createUser('thư ký');
        $this->actingAs($recipient);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('announcementBell')
            ->assertSee('Bạn đã xem hết thông báo.');

        $csrfToken = 'announcement-bell-token';
        $this->withSession(['_token' => $csrfToken]);
        $this->actingAs($author)
            ->post(route('admin.announcements.store'), [
                '_token' => $csrfToken,
                'title' => 'Lịch họp toàn đơn vị',
                'content' => 'Có mặt đúng giờ.',
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::firstOrFail();
        $this->actingAs($recipient);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('1 chưa xem')
            ->assertSee('Lịch họp toàn đơn vị');

        $this->getJson(route('announcements.notifications'))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.title', 'Lịch họp toàn đơn vị');

        $csrfToken = 'announcement-read-token';
        $this->withSession(['_token' => $csrfToken]);
        $this->post(route('announcements.read', $announcement), ['_token' => $csrfToken])
            ->assertRedirect(route('announcements.index').'#announcement-'.$announcement->id);

        $this->assertDatabaseHas('announcement_reads', [
            'announcement_id' => $announcement->id,
            'user_id' => $recipient->id,
        ]);
        $this->getJson(route('announcements.notifications'))
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonCount(0, 'items');

        $this->actingAs($author)->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Thông báo tiếp theo',
            'content' => 'Thông tin mới.',
        ]);

        $this->actingAs($recipient)
            ->getJson(route('announcements.notifications'))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.title', 'Thông báo tiếp theo');
    }

    public function test_guests_cannot_mark_announcement_notifications_as_read(): void
    {
        $announcement = Announcement::create([
            'title' => 'Thông báo dành cho nhân sự',
            'content' => 'Nội dung thông báo.',
        ]);

        $csrfToken = 'guest-read-token';
        $this->withSession(['_token' => $csrfToken]);
        $this->post(route('announcements.read', $announcement), ['_token' => $csrfToken])
            ->assertRedirect(route('login'));
    }

    private function createUser(string $role): User
    {
        return User::create([
            'username' => fake()->unique()->userName(),
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
