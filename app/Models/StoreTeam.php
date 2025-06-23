<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreTeam extends Model
{
    protected $table = 'store_teams';

    protected $fillable = [
        'team_id',
        'online_store_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function onlineStore()
    {
        return $this->belongsTo(OnlineStore::class);
    }


}
