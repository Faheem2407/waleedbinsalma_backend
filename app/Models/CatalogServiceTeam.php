<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogServiceTeam extends Pivot
{
    protected $table = 'catalog_service_teams';

    protected $fillable = [
        'catalog_service_id',
        'team_id',
    ];

}

