<?php
namespace App\Events;

use AllowDynamicProperties;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

#[AllowDynamicProperties] class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $roomId = $this->message->room_id;

        return [
            new PrivateChannel("chat.room.{$roomId}"),
            new PresenceChannel('presence-chat.main'),
        ];
    }

    public function broadcastWith(): array
    {
        // this array sends to front
        return [
                'message_id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'recipient_id' => $this->message->recipient_id,
                'message' => $this->message->message ?? null,
                'picture_url' => $this->message->picture_url ?? null,
                'formatted_date' => $this->message->created_at->format('F j, Y'),
                'formatted_time' => $this->message->created_at->format('g:i A'),
            ];
    }
}
