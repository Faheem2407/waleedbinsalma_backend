<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'online_store_id',
        'start_date',
        'end_date',
        'is_renew'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

}
