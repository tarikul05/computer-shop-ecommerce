<?php

namespace App\Modules\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutocompleteRequest extends FormRequest
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
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Get the search query
     */
    public function getSearchQuery(): string
    {
        return $this->input('q', '');
    }

    /**
     * Get the limit
     */
    public function getLimit(): int
    {
        return (int) $this->input('limit', 8);
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            'q.required' => 'Search query is required.',
            'q.min' => 'Search query must be at least 2 characters.',
            'q.max' => 'Search query cannot exceed 100 characters.',
        ];
    }
}
