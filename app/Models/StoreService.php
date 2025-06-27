<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreService extends Model
{
    protected $table = 'store_services';

    protected $fillable = [
        'online_store_id',
        'catalog_service_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function store()
    {
        return $this->belongsTo(OnlineStore::class, 'online_store_id');
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_services');
    }

    public function catalogService()
    {
        return $this->belongsTo(CatalogService::class, 'catalog_service_id');
    }

    public function appointmentServices()
    {
        return $this->hasMany(AppointmentService::class);
    }
}
