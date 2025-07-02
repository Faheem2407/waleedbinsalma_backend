<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    protected $dates = ['deleted_at'];

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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
