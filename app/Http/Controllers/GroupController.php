<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageSent;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\GroupMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function createGroup(): JsonResponse
    {
        $validated = $this->request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = ChatGroup::create([
            'name' => $validated['name'],
            'created_by' => auth()->id(),
        ]);

        // Add group creator user id
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json($group);
    }

    public function addMember( $groupId): JsonResponse
    {
        $validated = $this->request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        ChatGroupMember::create([
            'group_id' => $groupId,
            'user_id' => $validated['user_id'],
        ]);

        return response()->json(['message' => 'User added successfully']);
    }

    public function getGroupMessages($groupId): JsonResponse
    {
        $messages = GroupMessage::where('group_id', $groupId)
            ->with('sender')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage( $groupId): JsonResponse
    {
        $validated = $this->request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [];
        if ($this->request->hasFile('image')) {
            $path = $this->request->file('image')->store('uploads', 'public');
            $data['picture_url'] = asset("storage/{$path}");
        }

        $message = GroupMessage::create([
            'group_id' => $groupId,
            'sender_id' => auth()->id(),
            'message' => $this->request->message ?? '',
            'picture_url' => $data['picture_url'] ?? null,
        ]);

        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json($message);
    }
    public function getGroups(): JsonResponse
    {
        $user = auth()->user();

        $groups = ChatGroup::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('members:id,name,email')
        ->get();

        return response()->json([
            'groups' => $groups,
        ]);
    }
}

