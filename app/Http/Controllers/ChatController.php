<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sentMessages = null;
        $receivedMessages = null;
        if($user) {
            // Get all messages sent by this Auth user
            $sentMessages = $user->sentMessages;

            // Get all messages received by this Auth user
            $receivedMessages = $user->receivedMessages;
        }

        $allUsers = User::all();

        return view('main', compact('user','sentMessages', 'receivedMessages','allUsers'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'sender_id' => 'required',
            'message' => 'required|string|max:255',
        ]);
        $msgData = $request->all();
//        $sender = User::find($msgData['sender_id']);

        $message = new Message();
        $message->sender_id = $msgData['sender_id'];
        $message->recipient_id = $msgData['recipient_id'];
        $message->message = $msgData['message'];
        $message->username = 'empty';
        $message->save();

        broadcast(new MessageSent($message));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully!',
            'data' => $message,
        ]);
    }
}
