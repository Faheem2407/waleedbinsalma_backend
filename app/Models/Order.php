<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'online_store_id', 'total_amount', 'payment_status', 'payment_method'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
