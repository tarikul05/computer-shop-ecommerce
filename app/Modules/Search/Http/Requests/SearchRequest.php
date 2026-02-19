<?php

namespace App\Modules\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'brands' => ['nullable', 'array'],
            'brands.*' => ['string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'in_stock' => ['nullable', 'boolean'],
            'on_sale' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', 'in:relevance,price,name,created_at,rating,popularity'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get validated filters array
     */
    public function getFilters(): array
    {
        return array_filter([
            'category' => $this->input('category'),
            'brand' => $this->input('brand') ?? $this->input('brands'),
            'min_price' => $this->input('min_price'),
            'max_price' => $this->input('max_price'),
            'min_rating' => $this->input('min_rating'),
            'in_stock' => $this->boolean('in_stock'),
            'on_sale' => $this->boolean('on_sale'),
            'featured' => $this->boolean('featured'),
            'sort_by' => $this->input('sort_by'),
            'sort_order' => $this->input('sort_order'),
        ], fn($value) => $value !== null && $value !== '');
    }

    /**
     * Get the search query
     */
    public function getSearchQuery(): string
    {
        return $this->input('q', '');
    }

    /**
     * Get pagination limit
     */
    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 20);
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            'q.max' => 'Search query cannot exceed 200 characters.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
            'min_rating.min' => 'Minimum rating must be at least 1.',
            'min_rating.max' => 'Maximum rating cannot exceed 5.',
            'per_page.max' => 'Cannot request more than 100 items per page.',
        ];
    }
}
