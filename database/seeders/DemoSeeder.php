<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    // Sample messages to populate demo rooms
    private array $sampleConversations = [
        [
            ['Hey! How are you doing?', 0],
            ['Pretty good, thanks! You?', 1],
            ['Great! Just tried this new chat app 😄', 0],
            ['Yeah it works really well!', 1],
        ],
        [
            ['Did you see the game last night?', 0],
            ['Yes!! Incredible finish 🔥', 1],
            ['I know right, can\'t believe it', 0],
        ],
        [
            ['Can you send me that file?', 0],
            ['Sure, just a sec', 1],
            ['Got it, thanks!', 0],
            ['No problem 👍', 1],
        ],
        [
            ['Happy birthday!! 🎂🎉', 0],
            ['Aww thank you so much!!', 1],
            ['Hope you have an amazing day', 0],
        ],
        [
            ['Are you free this weekend?', 0],
            ['Saturday yes, Sunday no', 1],
            ['Perfect, let\'s meet Saturday then', 0],
            ['Sounds like a plan!', 1],
        ],
    ];

    public function run(): void
    {
        $userIds = User::whereIn('email', [
            'john_doe@example.com',
            'klaus_jenkins@example.com',
            'casandra_stray@example.com',
            'garry_border@example.com',
            'travis_scott@example.com',
        ])->pluck('id')->toArray();

        if (count($userIds) < 2) {
            $this->command->warn('Demo users not found. Skipping DemoSeeder.');
            return;
        }

        $convoIndex = 0;

        // Create a room between every pair of test users
        foreach ($userIds as $i => $userId) {
            foreach (array_slice($userIds, $i + 1) as $friendId) {
                $room = Room::firstOrCreate(
                    [
                        'auth_user'   => min($userId, $friendId),
                        'friend_user' => max($userId, $friendId),
                    ]
                );

                // Only seed messages if the room is empty
                if ($room->messages()->exists()) {
                    continue;
                }

                $conversation = $this->sampleConversations[$convoIndex % count($this->sampleConversations)];
                $convoIndex++;

                $baseTime = now()->subHours(rand(1, 72));

                foreach ($conversation as $offset => [$text, $senderIndex]) {
                    $sender    = $senderIndex === 0 ? $userId : $friendId;
                    $recipient = $senderIndex === 0 ? $friendId : $userId;

                    Message::create([
                        'room_id'      => $room->id,
                        'sender_id'    => $sender,
                        'recipient_id' => $recipient,
                        'message'      => $text,
                        'created_at'   => $baseTime->copy()->addMinutes($offset * rand(1, 10)),
                        'updated_at'   => $baseTime->copy()->addMinutes($offset * rand(1, 10)),
                    ]);
                }
            }
        }

        $this->command->info('Demo rooms and messages seeded successfully.');
    }
}
