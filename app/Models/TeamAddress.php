<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAddress extends Model
{
    protected $fillable = [
        'team_id',
        'address_name',
        'address',
        'apt_suite',
        'district',
        'city',
        'country',
        'state',
        'post_code',
    ];

    protected $hidden = ['created_at', 'updated_at'];
    
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

