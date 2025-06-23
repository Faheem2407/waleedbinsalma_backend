<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReview extends Model
{
    protected $fillable = [
        'client_avatar',
        'review',
        'rating',
        'shop_name',
        'shop_location',
        'status'
    ];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at'
    ];
}
