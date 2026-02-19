<?php

namespace App\Modules\Search\Models;

use Illuminate\Database\Eloquent\Model;

class PopularSearch extends Model
{
    protected $table = 'popular_searches';

    protected $fillable = [
        'query',
        'search_count',
        'click_count',
        'conversion_count',
        'last_searched_at',
    ];

    protected $casts = [
        'search_count' => 'integer',
        'click_count' => 'integer',
        'conversion_count' => 'integer',
        'last_searched_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderByDesc('search_count')
            ->limit($limit);
    }

    public function scopeTrending($query, int $days = 7, int $limit = 10)
    {
        return $query->where('last_searched_at', '>=', now()->subDays($days))
            ->orderByDesc('search_count')
            ->limit($limit);
    }

    public function scopeWithConversions($query)
    {
        return $query->where('conversion_count', '>', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    public function incrementSearchCount(): void
    {
        $this->increment('search_count');
        $this->update(['last_searched_at' => now()]);
    }

    public function incrementClickCount(): void
    {
        $this->increment('click_count');
    }

    public function incrementConversionCount(): void
    {
        $this->increment('conversion_count');
    }

    /**
     * Get click-through rate
     */
    public function getCtrAttribute(): float
    {
        if ($this->search_count === 0) {
            return 0;
        }
        return round(($this->click_count / $this->search_count) * 100, 2);
    }

    /**
     * Get conversion rate
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->click_count === 0) {
            return 0;
        }
        return round(($this->conversion_count / $this->click_count) * 100, 2);
    }
}
