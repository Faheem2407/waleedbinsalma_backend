<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    protected $table = 'opening_hours';

    public $fillable = [
        'online_store_id',
        'day_name',
        'morning_start_time',
        'morning_end_time',
        'evening_start_time',
        'evening_end_time',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function onlineStore()
    {
        return $this->belongsTo(OnlineStore::class);
    }
}
