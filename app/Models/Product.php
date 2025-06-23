<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }
}
