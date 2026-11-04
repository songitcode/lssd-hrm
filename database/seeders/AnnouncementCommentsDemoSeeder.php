<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AnnouncementComment;
use App\Models\AnnouncementReaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementCommentsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::firstOrCreate(
            ['username' => 'demo_thuky'],
            ['password' => 'password', 'role' => 'thư ký']
        );
        $members = collect(range(1, 8))->map(fn (int $number) => User::firstOrCreate(
            ['username' => 'demo_nhanvien_'.$number],
            ['password' => 'password', 'role' => 'cảnh sát viên']
        ));

        $announcement = Announcement::firstOrCreate(
            ['created_by' => $author->id, 'title' => '[DEMO] Thông báo với bình luận mẫu'],
            ['content' => 'Bài đăng mẫu để kiểm tra giao diện cảm xúc, bình luận dài và trả lời dạng luồng. Có thể chạy lại seeder mà không tạo thêm bài mẫu.']
        );

        $longBody = 'Đây là bình luận dài dùng để kiểm tra chức năng thu gọn và mở rộng nội dung. '
            .'Trên giao diện chỉ hiển thị phần xem trước; người đọc có thể chọn “Xem thêm” để đọc toàn bộ. '
            .'Nội dung có xuống dòng, nhiều đoạn và đủ dài để kiểm tra cách ngắt dòng, độ rộng, khả năng đọc trên thiết bị di động. ';

        $topLevelComments = collect(range(1, 8))->map(function (int $number) use (
            $announcement,
            $members,
            $longBody
        ): AnnouncementComment {
            return AnnouncementComment::firstOrCreate(
                [
                    'announcement_id' => $announcement->id,
                    'user_id' => $members[($number - 1) % $members->count()]->id,
                    'parent_id' => null,
                    'body' => $number <= 4
                        ? '[DEMO] Bình luận '.$number.'. '.$longBody.str_repeat('Chi tiết mẫu '.$number.'. ', 12)
                        : '[DEMO] Bình luận ngắn '.$number.'. Cảm ơn đã cập nhật thông tin.',
                ]
            );
        });

        $firstComment = $topLevelComments->first();
        $firstReply = AnnouncementComment::firstOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $members[4]->id,
                'parent_id' => $firstComment->id,
                'body' => '[DEMO] Phản hồi cấp một: Mình đồng ý và xin bổ sung một ý nhỏ.',
            ]
        );
        AnnouncementComment::firstOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $members[5]->id,
                'parent_id' => $firstReply->id,
                'body' => '[DEMO] Phản hồi cấp hai: Đây là ví dụ trả lời lồng nhau để kiểm tra thụt lề dạng cầu thang.',
            ]
        );
        AnnouncementComment::firstOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $members[6]->id,
                'parent_id' => $firstComment->id,
                'body' => '[DEMO] Một phản hồi khác trong cùng luồng.',
            ]
        );

        foreach (['like', 'love', 'haha', 'care'] as $index => $type) {
            AnnouncementReaction::updateOrCreate(
                ['announcement_id' => $announcement->id, 'user_id' => $members[$index]->id],
                ['type' => $type]
            );
        }
    }
}
