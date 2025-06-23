<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenities extends Model
{
    protected $guarded = [];
    protected $table = 'amenities';
    protected $hidden = ['created_at', 'updated_at'];


}
