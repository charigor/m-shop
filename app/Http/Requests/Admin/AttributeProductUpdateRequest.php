<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttributeProductUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'integer|min:0',
            'price' => 'numeric|min:0',
            'rebate' => 'numeric|min:0',
            'width' => 'numeric|min:0|max:1000',
            'height' => 'numeric|min:0|max:1000',
            'depth' => 'numeric|min:0|max:1000',
            'weight' => 'numeric|min:0|max:1000',
            'reference' => 'string',
            'media' => 'array',
        ];
    }
}
