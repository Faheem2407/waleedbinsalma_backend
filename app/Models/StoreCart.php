<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCart extends Model
{
    protected $table = 'store_carts';

    protected $fillable = [
        'user_id',
        'online_store_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

