<?php

namespace App\Modules\Search\Models;

use Illuminate\Database\Eloquent\Model;

class SearchSynonym extends Model
{
    protected $table = 'search_synonyms';

    protected $fillable = [
        'term',
        'synonyms',
        'is_active',
    ];

    protected $casts = [
        'synonyms' => 'array',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTerm($query, string $term)
    {
        return $query->where('term', strtolower($term));
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all synonyms for a term including the term itself
     */
    public function getAllTerms(): array
    {
        return array_merge([$this->term], $this->synonyms ?? []);
    }

    /**
     * Check if a word is a synonym
     */
    public function isSynonym(string $word): bool
    {
        return in_array(strtolower($word), $this->synonyms ?? []);
    }
}
