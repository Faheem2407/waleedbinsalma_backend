<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogService extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'catalog_service_category_id',
        'business_profile_id',
        'service_id',
        'name',
        'description',
        'duration',
        'price_type',
        'price',
    ];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(CatalogServiceCategory::class, 'catalog_service_category_id');
    }

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function teamServices()
    {
        return $this->hasMany(TeamService::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'catalog_service_teams');
    }
}

