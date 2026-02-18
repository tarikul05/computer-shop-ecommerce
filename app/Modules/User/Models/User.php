<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get user's addresses
     */
    public function addresses()
    {
        return $this->hasMany(\App\Modules\User\Models\Address::class);
    }

    /**
     * Get user's orders
     */
    public function orders()
    {
        return $this->hasMany(\App\Modules\Order\Models\Order::class);
    }

    /**
     * Get user's wishlist items
     */
    public function wishlist()
    {
        return $this->hasMany(\App\Modules\Wishlist\Models\Wishlist::class);
    }

    /**
     * Get user's reviews
     */
    public function reviews()
    {
        return $this->hasMany(\App\Modules\Review\Models\Review::class);
    }

    /**
     * Get user's cart
     */
    public function cart()
    {
        return $this->hasOne(\App\Modules\Cart\Models\Cart::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get user's default address
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('is_default', true)->first();
    }
}
