<?php

namespace App\Http\Requests\Admin;

use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FeatureUpdateRequest extends FormRequest
{
    private array $langArr = array();
    private array $attr = array();
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        foreach(app()->shopLanguages as $lang){
            $this->langArr['lang.' . $lang . '.name'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
        }
        return  array_merge(
            $this->langArr,['guard_name' => 'required||unique:features,guard_name,'.$this->route('feature')->id]
        );
    }
    public function attributes(): array
    {
        foreach (app()->shopLanguages as $lang) {
            $this->attr['lang.' . $lang . '.name'] = 'name';
        }
        return $this->attr;
    }
}
