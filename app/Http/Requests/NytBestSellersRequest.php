<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NytBestSellersRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|array',
            'isbn.*' => 'string|max:13', // ISBN is typically 13 characters max
            'title' => 'nullable|string|max:255',
            'offset' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'isbn.*.max' => 'Each ISBN should be at most 13 characters long.',
            'offset.min' => 'The offset must be a non-negative integer.',
        ];
    }
}
