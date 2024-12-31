<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $table = 'group_messages';
    protected $fillable = ['group_id', 'sender_id', 'message', 'picture_url'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
