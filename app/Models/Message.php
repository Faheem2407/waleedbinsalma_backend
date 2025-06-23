<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        $data = Carbon::parse($this->created_at);

        return $data->isToday() ? 'Today' : ($data->isYesterday() ? 'Yesterday' : $data->format('j F Y'));
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now();
        });
    }

    protected $hidden = [
        'updated_at',
    ];
    
    public function conversation($query)
    {
        return $this->belongsTo(Conversation::class);
    }

}
