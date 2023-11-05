<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeatureCreateRequest extends FormRequest
{
    private array $langArr = [];

    private array $attr = [];

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
        foreach (app()->shopLanguages as $lang) {
            $this->langArr['lang.'.$lang.'.name'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
        }

        return array_merge(
            $this->langArr, ['guard_name' => 'required||unique:permissions,guard_name']
        );
    }

    public function attributes(): array
    {
        foreach (app()->shopLanguages as $lang) {
            $this->attr['lang.'.$lang.'.name'] = 'name';
        }

        return $this->attr;
    }
}
