<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_store_id',
        'code',
        'discount_amount',
        'discount_percentage',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
        'is_active',
        'minimum_amount',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function onlineStore()
    {
        return $this->belongsTo(OnlineStore::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function isValid($totalAmount)
    {
        $now = now();
        
        return $this->is_active &&
               (!$this->valid_from || $now->gte($this->valid_from)) &&
               (!$this->valid_until || $now->lte($this->valid_until)) &&
               (!$this->usage_limit || $this->used_count < $this->usage_limit) &&
               (!$this->minimum_amount || $totalAmount >= $this->minimum_amount);
    }

    public function calculateDiscount($totalAmount)
    {
        if ($this->discount_amount) {
            return min($this->discount_amount, $totalAmount);
        }
        
        if ($this->discount_percentage) {
            return ($this->discount_percentage / 100) * $totalAmount;
        }
        
        return 0;
    }
}