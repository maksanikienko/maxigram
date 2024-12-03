<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use App\Models\User;

class MessageService
{
    public function createMessage(User $sender, User $recipient, $request, int $roomId): Message
    {
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            $data['picture_url'] = asset("storage/{$path}");
        }
        $message = Message::create([
            'room_id' => $roomId,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'message' => $request->message ?? '',
            'picture_url' => $data['picture_url'] ?? null,
        ]);

        $message->formatted_date = $message->created_at->format('F j, Y');
        $message->formatted_time = $message->created_at->format('g:i A');
        $message->sender = $sender;
        $message->recipient = $recipient;

        SendMessageJob::dispatch($message, $sender, $recipient);

        return $message;
    }

}
