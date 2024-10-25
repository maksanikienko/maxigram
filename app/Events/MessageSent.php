<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {

        return [
            new PrivateChannel('chat.' . $this->message->sender_id . '.' . $this->message->recipient_id),
            new PrivateChannel('chat.' . $this->message->recipient_id . '.' . $this->message->sender_id)
        ];

    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'recipient_id' => $this->message->recipient_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
