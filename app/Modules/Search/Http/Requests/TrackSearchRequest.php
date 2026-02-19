<?php

namespace App\Modules\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackSearchRequest extends FormRequest
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
            'query' => ['required', 'string', 'max:200'],
            'type' => ['required', 'string', 'in:click,conversion'],
        ];
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'type.required' => 'Tracking type is required.',
            'type.in' => 'Tracking type must be either click or conversion.',
        ];
    }
}
