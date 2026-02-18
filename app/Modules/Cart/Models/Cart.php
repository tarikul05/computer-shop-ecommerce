<?php

namespace App\Modules\Cart\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
        'coupon_code',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(\App\Modules\Coupon\Models\Coupon::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get subtotal (before discount)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->total);
    }

    /**
     * Get total items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get total (after discount)
     */
    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - ($this->discount_amount ?? 0));
    }

    /**
     * Check if cart is empty
     */
    public function getIsEmptyAttribute(): bool
    {
        return $this->items->isEmpty();
    }
}
