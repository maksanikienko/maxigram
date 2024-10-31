<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use App\Services\MessageService;
class ChatController extends Controller
{
//    public function show(User $friend)
//    {
//        $friends = User::whereNot('id', auth()->id())->get();
//
//        return view('main', compact('friend','friends'));
//    }
    // Получить список всех комнат, в которых участвует аутентифицированный пользователь
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
                        'created_at' => $message->created_at
                    ];
                })
            ];
        });

        $friends = User::whereNot('id', auth()->id())->get();

        return view('main', compact('roomsWithData','friends'));

    }

    // Получить сообщения для конкретной комнаты
    public function getMessages(Room $room)
    {
        // Проверка, что текущий пользователь принадлежит комнате
        if ($room->auth_user !== auth()->id() && $room->friend_user !== auth()->id()) {
            abort(403);
        }

        // Загружаем сообщения комнаты, отсортированные по дате
        $messages = $room->messages()->with(['sender', 'recipient'])->orderBy('created_at', 'asc')->get();

        // Форматируем сообщения
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

        $message = $messageService->createMessage($sender, $friend, request()->input('message'), $room->id);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'message' => $message->message,
            'formatted_date' => $message->created_at->format('F j, Y'),
            'formatted_time' => $message->created_at->format('g:i A'),
        ]);
    }

//    public function getMessages(User $friend): Collection
//    {
//        $messages = Message::query()
//            ->where(function ($query) use ($friend) {
//                $query->where('sender_id', auth()->id())
//                    ->where('recipient_id', $friend->id);
//            })
//            ->orWhere(function ($query) use ($friend) {
//                $query->where('sender_id', $friend->id)
//                    ->where('recipient_id', auth()->id());
//            })
//            ->with(['sender', 'recipient'])
//            ->orderBy('id', 'asc')
//            ->get();
//
//        return $messages->map(function ($message) {
//            $message->formatted_date = $message->created_at->format('F j, Y');
//            $message->formatted_time = $message->created_at->format('g:i A');
//
//            return $message;
//        });
//    }

//    public function sendMessages(User $friend,MessageService $messageService): JsonResponse
//    {
//        $sender = auth()->user();
//        $message = $messageService->createMessage($sender, $friend, request()->input('message'));
//
//        broadcast(new MessageSent($message));
//
//        return response()->json([
//            'id' => $message->id,
//            'message' => $message->message,
//            'sender' => $message->sender,
//            'recipient' => $message->recipient,
//            'formatted_date' => $message->formatted_date,
//            'formatted_time' => $message->formatted_time,
//        ]);
//    }
}
