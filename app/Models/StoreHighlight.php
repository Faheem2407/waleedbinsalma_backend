<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreHighlight extends Model
{
    protected $table = 'store_highlights';

    protected $fillable = ['online_store_id', 'highlight_id'];

    protected $hidden = ['created_at', 'updated_at'];
    
    public function highlight()
    {
        return $this->belongsTo(Highlights::class);
    }
}
