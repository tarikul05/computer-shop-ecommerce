<?php

namespace App\Modules\Search\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'formatted_price' => '৳' . number_format($this->price, 2),
            'compare_price' => $this->compare_price,
            'formatted_compare_price' => $this->compare_price ? '৳' . number_format($this->compare_price, 2) : null,
            'discount_percentage' => $this->discount_percentage,
            'in_stock' => $this->quantity > 0,
            'quantity' => $this->quantity,
            'is_featured' => $this->is_featured,
            'rating' => round($this->rating, 1),
            'reviews_count' => $this->reviews_count,
            'image' => $this->whenLoaded('images', function () {
                $primaryImage = $this->images->first();
                return $primaryImage?->image;
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ];
            }),
            'relevance_score' => $this->when(isset($this->relevance_score), $this->relevance_score),
        ];
    }
}
