<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementCommentActivity extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $announcementId,
        private readonly int $commentId,
        private readonly int $actorId,
        private readonly string $actorName,
        private readonly string $body,
        private readonly bool $isReply,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcementId,
            'comment_id' => $this->commentId,
            'actor_id' => $this->actorId,
            'actor_name' => $this->actorName,
            'body' => $this->body,
            'is_reply' => $this->isReply,
        ];
    }
}
