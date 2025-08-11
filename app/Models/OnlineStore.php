<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineStore extends Model
{
    protected $table = 'online_stores';
    protected $guarded = [];

    protected $hidden = ['status','created_at', 'updated_at'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function openingHours()
    {
        return $this->hasMany(OpeningHour::class);
    }

    public function storeImages()
    {
        return $this->hasMany(StoreImage::class);
    }

    public function storeAmenities()
    {
        return $this->hasMany(StoreAmenity::class);
    }

    public function storeServices()
    {
        return $this->hasMany(StoreService::class);
    }
    
    public function storeHighlights()
    {
        return $this->hasMany(StoreHighlight::class);
    }

    public function storeValues()
    {
        return $this->hasMany(StoreValue::class);
    }

    public function storeTeams()
    {
        return $this->hasMany(StoreTeam::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'online_store_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'online_store_id');
    }

}
