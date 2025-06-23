<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreValue extends Model
{
    protected $table = 'store_values';

    protected $fillable = [
        'online_store_id',
        'value_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function value()
    {
        return $this->belongsTo(Values::class);
    }
}
