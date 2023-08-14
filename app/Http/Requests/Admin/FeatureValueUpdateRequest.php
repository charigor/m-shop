<?php

namespace App\Http\Requests\Admin;

use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;

class FeatureValueUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    private array $langArr = array();
    private array $attr = array();
    public function __construct(){
        $this->langs = Lang::whereActive(1)->get()->pluck('code');
    }
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

        foreach($this->langs as $lang){
            $this->langArr['lang.' . $lang . '.value'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
        }
       return  array_merge(
            $this->langArr,
            [
                'feature_id' => 'required|integer',
            ]
        );
    }
    public function attributes(): array
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.' . $lang . '.value'] = 'value';
        }
        $this->attr['feature_id'] = 'feature';
        return $this->attr;
    }
}
