<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Events\MessageSent;
use App\Services\MessageService;
class ChatController extends Controller
{
    public function getRooms()
    {
        $user = auth()->user();

        $rooms = Room::where('auth_user', $user->id)
            ->orWhere('friend_user', $user->id)
            ->with([
                'messages',
                'authUser',
                'friendUser'
            ])
            ->get();

        $roomsWithData = $rooms->map(function ($room) use ($user) {
            // Friend user
            $otherUser = $room->auth_user === $user->id ? $room->friendUser : $room->authUser;

            return [
                'room_id' => $room->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'image' => $otherUser->image,
                    'is_online' => $otherUser->is_online
                ],
                'messages' => $room->messages->map(function ($message) {
                    return [
                        'message_id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'recipient_id' => $message->recipient_id,
                        'message' => $message->message,
                        'picture_url' => $message->picture_url,
                        'formatted_date' => $message->created_at->format('F j, Y'),
                        'formatted_time' => $message->created_at->format('g:i A'),
                    ];
                })
            ];
        });

//        $friends = User::whereNot('id', auth()->id())->get();
        $profileUrl = route('profile.edit');

        return view('main', compact('roomsWithData','profileUrl' ));

    }
    public function getMessages(Room $room)
    {
        if ($room->auth_user !== auth()->id() && $room->friend_user !== auth()->id()) {
            abort(403);
        }

        $messages = $room->messages()->with(['sender', 'recipient'])->orderBy('created_at', 'asc')->get();

        return $messages->map(function ($message) {
            $message->formatted_date = $message->created_at->format('F j, Y');
            $message->formatted_time = $message->created_at->format('g:i A');
            return $message;
        });
    }
    public function sendMessages(Room $room, MessageService $messageService): JsonResponse
    {
        $sender = auth()->user();
        $friend = ($room->auth_user === $sender->id) ? User::find($room->friend_user) : User::find($room->auth_user);

        $message = $messageService->createMessage($sender, $friend, request(), $room->id);

        return response()->json([
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'message' => $message->message ?? null,
            'picture_url' => $message->picture_url ?? null,
            'formatted_date' => $message->created_at->format('F j, Y'),
            'formatted_time' => $message->created_at->format('g:i A'),
        ]);
    }
}
