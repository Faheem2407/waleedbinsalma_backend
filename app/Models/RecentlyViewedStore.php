<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyViewedStore extends Model
{
    protected $table = 'recently_viewed_stores';

    protected $fillable = [
        'user_id',
        'online_store_id'
    ];

}
