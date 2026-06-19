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

        return (new WebPushMessage())
            ->title($this->sender->name)
            ->body($body)
            ->icon($this->sender->image ?: '/icons/icon-192.png')
            ->badge('/icons/icon-192.png')
            ->data(['url' => '/chat/rooms']);
    }
}
