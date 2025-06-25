<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessDocument()
    {
        return $this->hasOne(BusinessDocument::class);
    }

    public function businessServices()
    {
        return $this->hasMany(BusinessService::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BusinessBankDetails::class);
    }

    public function onlineStore()
    {
        return $this->hasOne(OnlineStore::class);
    }
}
