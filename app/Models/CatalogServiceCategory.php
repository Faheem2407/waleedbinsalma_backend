<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogServiceCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_profile_id',
        'name',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function catalogServices()
    {
        return $this->hasMany(CatalogService::class);
    }
}

