<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamService extends Model
{
    protected $fillable = [
        'team_id',
        'catalog_service_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function catalogService()
    {
        return $this->belongsTo(CatalogService::class);
    }
}
