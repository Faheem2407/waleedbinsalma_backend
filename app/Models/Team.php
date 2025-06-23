<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'business_profile_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'birthday',
        'job_title',
        'start_date',
        'end_date',
        'employment_type',
        'employee_id',
        'photo',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function teamAddresses()
    {
        return $this->hasMany(TeamAddress::class);
    }

    public function teamServices()
    {
        return $this->hasMany(TeamService::class);
    }

    public function catalogServices()
    {
        return $this->belongsToMany(CatalogService::class, 'catalog_service_teams');
    }
}

