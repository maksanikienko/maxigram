<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['auth_user', 'friend_user'];

    /**
     * Get all messages in the room.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class,'room_id');
    }

    /**
     * Get the authenticated user of the room.
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_user');
    }

    /**
     * Get the friend user of the room.
     */
    public function friendUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_user');
    }
}
