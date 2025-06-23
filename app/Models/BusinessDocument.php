<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessDocument extends Model
{
    protected $guarded=[];

    public function businessProfile(){
        return $this->belongsTo(BusinessProfile::class);
    }
}
