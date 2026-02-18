<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'quantity',
        'low_stock_threshold',
        'weight',
        'length',
        'width',
        'height',
        'is_active',
        'is_featured',
        'is_new',
        'is_best_seller',
        'warranty_info',
        'warranty_period',
        'meta_title',
        'meta_description',
        'views_count',
        'sales_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_best_seller' => 'boolean',
            'views_count' => 'integer',
            'sales_count' => 'integer',
        ];
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get product category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product brand
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get product images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get primary image
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get product specifications
     */
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    /**
     * Get product reviews
     */
    public function reviews()
    {
        return $this->hasMany(\App\Modules\Review\Models\Review::class);
    }

    /**
     * Get approved reviews
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('status', 'approved');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get new products
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope to get best sellers
     */
    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    /**
     * Scope to get in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope to get low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold')
                     ->where('quantity', '>', 0);
    }

    /**
     * Scope to get out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope to filter by category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by brand
     */
    public function scopeOfBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope to filter by price range
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope to search products
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('short_description', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Check if product is on sale
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->is_on_sale) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        }

        if ($this->quantity <= $this->low_stock_threshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Check if product is in stock
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get reviews count
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primaryImage?->image_url;
    }
}
