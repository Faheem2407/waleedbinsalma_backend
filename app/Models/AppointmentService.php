<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    protected $fillable = [
        'appointment_id',
        'store_service_id',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function storeService()
    {
        return $this->belongsTo(StoreService::class);
    }
}
