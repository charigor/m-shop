<?php

namespace App\Http\Requests\Admin\Image;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUploadImageRequest extends FormRequest
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
        $rules = 'image|mimes:jpeg,png,jpg,gif';
        // Validates file size
        if(request()->has('size'))  { $rules .= '|max:' . (request()->input('size') * 1024).'|';}
        // If width or height is preset - we are validating it as an image
        if(request()->has('width') || request()->has('height')) {
            $rules .= sprintf(
                'dimensions:max_width=%s,max_height=%s',
                request()->input('width', 100000),
                request()->input('height', 100000)
            );
        }
        return [
            'file' => $rules
        ];
    }
}
