<?php

namespace App\Modules\Search\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchResultCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = SearchResultResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => $this->collection,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'query' => $request->input('q', ''),
                'filters_applied' => array_filter([
                    'category' => $request->input('category'),
                    'brand' => $request->input('brand'),
                    'min_price' => $request->input('min_price'),
                    'max_price' => $request->input('max_price'),
                    'min_rating' => $request->input('min_rating'),
                    'in_stock' => $request->boolean('in_stock') ?: null,
                    'on_sale' => $request->boolean('on_sale') ?: null,
                ], fn($v) => $v !== null),
                'sort_by' => $request->input('sort_by', 'relevance'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ],
        ];
    }
}
