<?php

namespace App\Http\Requests\Admin;

use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;

class LangUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:30',
            'code' => 'required|max:2',
            'date_format' => 'required|string|in:'.implode(',', Lang::DATE_FORMAT),
            'date_format_full' => 'required|string|in:'.implode(',', Lang::DATE_FORMAT_FULL),
            'active' => 'required|integer|in:'.implode(',', array_flip(Lang::ACTIVE)),
        ];
    }
}
