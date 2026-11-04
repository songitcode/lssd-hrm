<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementCommentActivity;
use App\Support\DiscordMarkdownRenderer;
use Database\Seeders\AnnouncementCommentsDemoSeeder;
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

    public function test_announcement_detail_page_shows_full_post_and_all_images(): void
    {
        Storage::fake('public');
        $imagePaths = ['announcements/first.png', 'announcements/second.png'];
        Storage::disk('public')->put($imagePaths[0], 'first-image');
        Storage::disk('public')->put($imagePaths[1], 'second-image');
        $announcement = Announcement::create([
            'title' => 'Thông báo có nhiều ảnh',
            'content' => 'Nội dung đầy đủ của bài đăng.',
            'image_path' => $imagePaths[0],
            'image_paths' => $imagePaths,
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('Thông báo có nhiều ảnh')
            ->assertSee('Nội dung đầy đủ của bài đăng.')
            ->assertSee(route('announcements.image', [$announcement, 0], false))
            ->assertSee(route('announcements.image', [$announcement, 1], false));

        $this->get(route('announcements.image', [$announcement, 1]))
            ->assertOk()
            ->assertStreamedContent('second-image');
    }

    public function test_announcement_reactions_can_be_added_changed_and_removed(): void
    {
        $announcement = Announcement::create([
            'title' => 'Thông báo được quan tâm',
            'content' => 'Nội dung.',
        ]);
        $user = $this->createUser('cảnh sát viên');
        $this->actingAs($user);
        $csrfToken = 'announcement-reaction-token';
        $this->withSession(['_token' => $csrfToken]);

        $this->post(route('announcements.reactions.toggle', $announcement), [
            '_token' => $csrfToken,
            'type' => 'like',
        ])
            ->assertRedirect(route('announcements.show', $announcement).'#reactions');
        $this->assertDatabaseHas('announcement_reactions', [
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('data-list-reactions')
            ->assertSee('>1</span> cảm xúc', false);

        $this->post(route('announcements.reactions.toggle', $announcement), [
            '_token' => $csrfToken,
            'type' => 'love',
        ]);
        $this->assertDatabaseHas('announcement_reactions', [
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'type' => 'love',
        ]);
        $this->assertDatabaseCount('announcement_reactions', 1);

        $this->post(route('announcements.reactions.toggle', $announcement), [
            '_token' => $csrfToken,
            'type' => 'love',
        ]);
        $this->assertDatabaseMissing('announcement_reactions', [
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_announcement_comments_are_limited_to_authenticated_users_and_rendered_as_text(): void
    {
        $announcement = Announcement::create([
            'title' => 'Thông báo có bình luận',
            'content' => 'Nội dung.',
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('data-total-comments')
            ->assertSee('Đăng nhập');

        $csrfToken = 'announcement-comment-token';
        $user = $this->createUser('cảnh sát viên');
        $this->actingAs($user)->withSession(['_token' => $csrfToken]);

        $this->post(route('announcements.comments.store', $announcement), [
            '_token' => $csrfToken,
            'body' => '<script>alert(1)</script> Bình luận hữu ích.',
        ])->assertRedirect(route('announcements.show', $announcement).'#comments');

        $this->assertDatabaseHas('announcement_comments', [
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'body' => '<script>alert(1)</script> Bình luận hữu ích.',
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('data-total-comments>1</span>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; Bình luận hữu ích.', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->post(route('announcements.reactions.toggle', $announcement), [
            '_token' => $csrfToken,
            'type' => 'unknown',
        ])
            ->assertSessionHasErrors('type');
        $this->post(route('announcements.comments.store', $announcement), [
            '_token' => $csrfToken,
            'body' => str_repeat('a', 2001),
        ])->assertSessionHasErrors('body');
    }

    public function test_announcement_interaction_state_updates_realtime_summaries_and_threads(): void
    {
        $announcement = Announcement::create([
            'title' => 'Bài đăng realtime',
            'content' => 'Nội dung.',
        ]);
        $author = $this->createUser('cảnh sát viên');
        $replier = $this->createUser('cảnh sát viên');
        $comment = $announcement->comments()->create([
            'user_id' => $author->id,
            'body' => 'Bình luận gốc.',
        ]);
        $csrfToken = 'announcement-reply-token';
        $this->actingAs($replier)->withSession(['_token' => $csrfToken]);

        $this->post(route('announcements.comments.reply', [$announcement, $comment]), [
            '_token' => $csrfToken,
            'body' => 'Phản hồi trực tiếp.',
        ])->assertRedirect(route('announcements.show', $announcement).'#comment-'.$comment->id);

        $reply = $announcement->comments()->where('parent_id', $comment->id)->firstOrFail();
        $this->post(route('announcements.reactions.toggle', $announcement), [
            '_token' => $csrfToken,
            'type' => 'care',
        ]);

        $this->getJson(route('announcements.interactions.state', $announcement))
            ->assertOk()
            ->assertJsonPath('reactions.care', 1)
            ->assertJsonPath('reactions_count', 1)
            ->assertJsonPath('comments_count', 2)
            ->assertJsonPath('current_reaction', 'care')
            ->assertJsonPath('comments_html', fn (string $html) => str_contains($html, 'Bình luận gốc.')
                && str_contains($html, 'Phản hồi trực tiếp.'));

        $this->getJson(route('announcements.interactions.summaries', ['ids' => [$announcement->id]]))
            ->assertOk()
            ->assertJsonPath('0.reactions_count', 1)
            ->assertJsonPath('0.comments_count', 2);

        $this->delete(route('announcements.comments.destroy', [$announcement, $reply]), [
            '_token' => $csrfToken,
        ])->assertRedirect(route('announcements.show', $announcement).'#comments');
        $this->assertDatabaseMissing('announcement_comments', ['id' => $reply->id]);
    }

    public function test_users_can_delete_their_own_comments_and_admins_can_delete_any_comment(): void
    {
        $announcement = Announcement::create([
            'title' => 'Bài đăng có kiểm soát xóa',
            'content' => 'Nội dung.',
        ]);
        $owner = $this->createUser('cảnh sát viên');
        $otherUser = $this->createUser('cảnh sát viên');
        $admin = $this->createUser('admin');
        $comment = $announcement->comments()->create([
            'user_id' => $owner->id,
            'body' => 'Bình luận của chủ bài.',
        ]);
        $csrfToken = 'announcement-delete-token';
        $this->withSession(['_token' => $csrfToken]);

        $this->actingAs($otherUser)
            ->delete(route('announcements.comments.destroy', [$announcement, $comment]), [
                '_token' => $csrfToken,
            ])
            ->assertForbidden();
        $this->assertDatabaseHas('announcement_comments', ['id' => $comment->id]);

        $this->actingAs($owner)
            ->delete(route('announcements.comments.destroy', [$announcement, $comment]), [
                '_token' => $csrfToken,
            ])
            ->assertRedirect(route('announcements.show', $announcement).'#comments');
        $this->assertDatabaseMissing('announcement_comments', ['id' => $comment->id]);

        $otherComment = $announcement->comments()->create([
            'user_id' => $owner->id,
            'body' => 'Bình luận cần quản trị xóa.',
        ]);
        $this->actingAs($admin)
            ->delete(route('announcements.comments.destroy', [$announcement, $otherComment]), [
                '_token' => $csrfToken,
            ])
            ->assertRedirect(route('announcements.show', $announcement).'#comments');
        $this->assertDatabaseMissing('announcement_comments', ['id' => $otherComment->id]);
    }

    public function test_comment_notifications_are_sent_to_the_post_author_and_can_be_opened(): void
    {
        $author = $this->createUser('thư ký');
        $commenter = $this->createUser('cảnh sát viên');
        $announcement = $author->announcements()->create([
            'title' => 'Bài đăng có thông báo bình luận',
            'content' => 'Nội dung.',
        ]);
        $csrfToken = 'announcement-notification-token';

        $this->actingAs($commenter)->withSession(['_token' => $csrfToken])
            ->post(route('announcements.comments.store', $announcement), [
                '_token' => $csrfToken,
                'body' => 'Tôi đã bình luận.',
            ])
            ->assertRedirect(route('announcements.show', $announcement).'#comments');

        $notification = $author->notifications()->firstOrFail();
        $this->assertSame(AnnouncementCommentActivity::class, $notification->type);
        $this->assertSame('Tôi đã bình luận.', $notification->data['body']);

        $this->actingAs($author)
            ->getJson(route('announcements.notifications'))
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonFragment([
                'method' => 'GET',
                'excerpt' => 'Tôi đã bình luận.',
            ]);

        $this->get(route('announcements.comment-notifications.open', $notification->id))
            ->assertRedirect(route('announcements.show', [
                'announcement' => $announcement,
                'focus_comment' => $notification->data['comment_id'],
            ]).'#comment-'.$notification->data['comment_id']);
        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($author)->withSession(['_token' => $csrfToken])
            ->post(route('announcements.comments.store', $announcement), [
                '_token' => $csrfToken,
                'body' => 'Chủ bài tự bình luận.',
            ]);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_replies_can_nest_and_comment_lists_load_three_then_five_more(): void
    {
        $announcement = Announcement::create([
            'title' => 'Bài đăng dạng hội thoại',
            'content' => 'Nội dung.',
        ]);
        $users = collect(range(1, 10))->map(fn () => $this->createUser('cảnh sát viên'));
        foreach (range(1, 9) as $index) {
            $announcement->comments()->create([
                'user_id' => $users[$index - 1]->id,
                'body' => 'Bình luận thứ '.$index,
            ]);
        }
        $topComment = $announcement->comments()->latest('id')->firstOrFail();
        $reply = $announcement->comments()->create([
            'user_id' => $users[9]->id,
            'parent_id' => $topComment->id,
            'body' => 'Phản hồi cấp một.',
        ]);
        $csrfToken = 'announcement-thread-token';

        $this->actingAs($users[0])->withSession(['_token' => $csrfToken])
            ->post(route('announcements.comments.reply', [$announcement, $reply]), [
                '_token' => $csrfToken,
                'body' => 'Phản hồi cấp hai.',
            ])
            ->assertRedirect(route('announcements.show', $announcement).'#comment-'.$reply->id);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $users[9]->id,
            'type' => AnnouncementCommentActivity::class,
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('Bình luận thứ 9')
            ->assertSee('Bình luận thứ 8')
            ->assertSee('Bình luận thứ 7')
            ->assertDontSee('Bình luận thứ 6')
            ->assertSee('Phản hồi cấp một.')
            ->assertSee('Phản hồi cấp hai.');
        $this->get(route('announcements.show', [
            'announcement' => $announcement,
            'focus_comment' => $reply->id,
        ]))
            ->assertOk()
            ->assertSee('Phản hồi cấp hai.');

        $firstPage = $this->getJson(route('announcements.interactions.state', $announcement))
            ->assertOk()
            ->json();
        $this->assertNotNull($firstPage['comments_next_cursor']);

        $this->getJson(route('announcements.interactions.state', [
            'announcement' => $announcement->id,
            'comments_limit' => 5,
            'comments_cursor' => $firstPage['comments_next_cursor'],
        ]))
            ->assertOk()
            ->assertJsonPath('comments_next_cursor', fn ($cursor) => is_string($cursor))
            ->assertJsonPath('comments_html', fn (string $html) => str_contains($html, 'Bình luận thứ 6')
                && str_contains($html, 'Bình luận thứ 2'));
    }

    public function test_long_comments_have_collapsed_preview_and_expand_control(): void
    {
        $announcement = Announcement::create([
            'title' => 'Thông báo có bình luận dài',
            'content' => 'Nội dung.',
        ]);
        $longBody = str_repeat('Nội dung bình luận dài để kiểm tra thu gọn. ', 15);
        $announcement->comments()->create([
            'user_id' => $this->createUser('cảnh sát viên')->id,
            'body' => $longBody,
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('data-comment-preview')
            ->assertSee('data-comment-full')
            ->assertSee('data-comment-expand')
            ->assertSee('Xem thêm')
            ->assertSee(mb_substr($longBody, 0, 320).'…', false);
    }

    public function test_demo_announcement_seeder_is_repeatable(): void
    {
        $this->seed(AnnouncementCommentsDemoSeeder::class);
        $this->seed(AnnouncementCommentsDemoSeeder::class);

        $announcement = Announcement::where('title', '[DEMO] Thông báo với bình luận mẫu')->firstOrFail();
        $this->assertSame(11, $announcement->comments()->count());
        $this->assertSame(4, $announcement->reactions()->count());
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
        $this->assertNull($announcement->image_paths);
        Storage::disk('public')->assertMissing($replacementPath);

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'remove_image' => '1',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertNull($announcement->fresh()->image_url);
        $this->assertNull($announcement->fresh()->image_path);
        $this->assertNull($announcement->fresh()->image_paths);
    }

    public function test_secretary_can_upload_multiple_announcement_images_and_replace_them(): void
    {
        Storage::fake('public');
        $csrfToken = 'announcement-multiple-images-token';
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
        $this->actingAs($this->createUser('thư ký'));
        $this->withSession(['_token' => $csrfToken]);

        $this->post(route('admin.announcements.store'), [
            '_token' => $csrfToken,
            'title' => 'Bộ ảnh thông báo',
            'content' => 'Nội dung.',
            'image_files' => [
                UploadedFile::fake()->createWithContent('first.png', $pngContent),
                UploadedFile::fake()->createWithContent('second.png', $pngContent),
            ],
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::firstOrFail();
        $originalPaths = $announcement->image_paths;
        $this->assertCount(2, $originalPaths);
        $this->assertSame($originalPaths[0], $announcement->image_path);
        foreach ($originalPaths as $path) {
            Storage::disk('public')->assertExists($path);
        }
        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee(route('announcements.image', [$announcement, 0], false))
            ->assertSee(route('announcements.image', [$announcement, 1], false));

        $this->put(route('admin.announcements.update', $announcement), [
            '_token' => $csrfToken,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'image_files' => [UploadedFile::fake()->createWithContent('replacement.png', $pngContent)],
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement->refresh();
        $this->assertCount(1, $announcement->image_paths);
        foreach ($originalPaths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
        Storage::disk('public')->assertExists($announcement->image_paths[0]);
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
            ->assertRedirect(route('announcements.show', $announcement));

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
