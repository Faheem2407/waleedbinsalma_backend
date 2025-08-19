<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'online_store_id',
        'user_id',
        'appointment_type',
        'date',
        'time',
        'booking_notes',
        'status',
        'discount_code_id',
        'discount_amount_applied',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function onlineStore()
    {
        return $this->belongsTo(OnlineStore::class, 'online_store_id');
    }

    public function storeServices()
    {
        return $this->belongsToMany(StoreService::class, 'appointment_services');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function appointmentServices()
    {
        return $this->belongsToMany(StoreService::class, 'appointment_services', 'appointment_id', 'store_service_id');
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function teamAssignments()
    {
        return $this->hasMany(AppointmentTeamAssignment::class);
    }
}