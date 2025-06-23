<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Return the primary key of the user (id)
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'provider',
        'provider_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'agree_to_terms' => 'boolean',
            'is_premium' => 'boolean',
            'id' => 'integer',
        ];
    }


    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class,'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id', 'id');
    }
    
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(OnlineStore::class, 'store_bookmarks', 'user_id', 'online_store_id');
    }

    public function products()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // return product details via product id

    public function productDetails()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

}
