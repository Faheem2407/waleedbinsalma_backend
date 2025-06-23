<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $fillable = [
        'business_profile_id',
        'name',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
