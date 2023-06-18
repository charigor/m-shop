<?php

namespace App\Http\Requests\Admin;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' =>  'required|string|max:255',
            'email' =>  'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|min:8|confirmed',
            'roles' => 'required',
            'avatar' => 'array'
        ];
    }
}
