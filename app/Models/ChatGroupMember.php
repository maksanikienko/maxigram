<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
    protected $table = 'chat_group_members';
    protected $fillable = ['group_id', 'user_id'];

    public function group()
    {
        return $this->belongsTo(ChatGroup::class);
    }
}
