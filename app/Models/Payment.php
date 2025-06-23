<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'appointment_id',
        'payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        // 'receipt_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

