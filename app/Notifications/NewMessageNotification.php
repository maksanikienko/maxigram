<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewMessageNotification extends Notification
{
    public function __construct(
        private readonly Message $message,
        private readonly User    $sender,
    ) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $body = $this->message->message
            ? mb_substr($this->message->message, 0, 120)
            : '📷 Image';

        return WebPushMessage::create()
            ->title($this->sender->name)
            ->body($body)
            ->icon($this->sender->image ?: '/favicon.ico')
            ->badge('/favicon.ico')
            ->data(['url' => '/chat/rooms']);
    }
}
