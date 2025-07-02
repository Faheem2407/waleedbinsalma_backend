<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'subscription_id',
        'payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
