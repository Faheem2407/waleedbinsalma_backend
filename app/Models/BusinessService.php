<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessService extends Model
{
    protected $guarded=[];

    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function businessProfile(){
        return $this->belongsTo(BusinessProfile::class);
    }
}
