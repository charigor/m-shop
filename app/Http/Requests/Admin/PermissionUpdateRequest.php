<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
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
            'name' =>  'required|max:30|unique:permissions,name,'.$this->id,
            'guard_name' =>  'required|regex:/^[a-zA-Z]+$/u|max:30|unique:permissions,guard_name,'.$this->id,
        ];
    }
}
