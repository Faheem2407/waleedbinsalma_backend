<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreAmenity extends Model
{
    protected $table = 'store_amenities';

    protected $fillable = [
        'online_store_id',
        'amenity_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function amenity()
    {
        return $this->belongsTo(Amenities::class);
    }
}
