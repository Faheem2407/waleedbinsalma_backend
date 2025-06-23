<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogServiceCategory extends Model
{
    protected $fillable = [
        'business_profile_id',
        'name',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function catalogServices()
    {
        return $this->hasMany(CatalogService::class);
    }
}

