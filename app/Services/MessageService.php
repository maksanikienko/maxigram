<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class MessageService
{
    public function createMessage(User $sender, User $recipient, string $text, int $roomId): Message
    {
        $message = Message::create([
            'room_id' => $roomId,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'message' => $text,
        ]);

        $message->formatted_date = $message->created_at->format('F j, Y');
        $message->formatted_time = $message->created_at->format('g:i A');
        $message->sender = $sender;
        $message->recipient = $recipient;

        return $message;
    }

}
