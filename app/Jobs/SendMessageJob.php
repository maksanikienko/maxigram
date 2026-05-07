<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Message $message;
    public User $sender;
    public User $recipient;

    public function __construct(Message $message, User $sender, User $recipient)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    public function handle(): void
    {
        $this->recipient->notify(
            new NewMessageNotification($this->message, $this->sender)
        );
    }
}
