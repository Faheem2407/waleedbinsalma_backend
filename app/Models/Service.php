<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable =['service_name','icon'];

    protected $hidden = ['created_at', 'updated_at'];

    public function businessServices(){
        return $this->hasMany(BusinessService::class);
    }

    public function catalogService()
    {
        return $this->hasOne(CatalogService::class, 'service_id');
    }

    public function storeServices()
    {
        return $this->hasMany(StoreService::class);
    }

}
