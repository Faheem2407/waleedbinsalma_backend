<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreBookmark extends Model
{
    protected $table = 'store_bookmarks';

    protected $fillable = [
        'online_store_id',
        'user_id',
    ];

    public function onlineStore()
    {
        return $this->belongsTo(OnlineStore::class);
    }
}
