<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sender_id'   => 'integer',
        'receiver_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // public function sender()
    // {
    //     return $this->belongsTo(User::class, 'sender_id', 'id');
    // }

    // public function receiver()
    // {
    //     return $this->belongsTo(User::class, 'receiver_id', 'id');
    // }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest()->take(1);
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'id')->where('is_read', false);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
