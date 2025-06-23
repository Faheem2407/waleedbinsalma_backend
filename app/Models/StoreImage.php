<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreImage extends Model
{
    protected $table = 'store_images';

    protected $fillable = [
        'online_store_id',
        'images',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
