<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessBankDetails extends Model
{
    protected $fillable = [
        'business_profile_id',
        'stripe_account_id',
        'tap_destination_id',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profile_id');
    }
}
