<?php

namespace App\Http\Requests\Admin;

use App\Models\AttributeGroup;
use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;

class AttributeCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    private array $langArr = [];

    private array $attr = [];

    public function __construct()
    {
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

        foreach ($this->langs as $lang) {
            $this->langArr['lang.'.$lang.'.name'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
        }

        return array_merge(
            $this->langArr,
            [
                'attribute_group_id' => 'required|integer',
                'color' => [
                    AttributeGroup::GROUP_TYPE[AttributeGroup::find(request('attribute_group_id'))['group_type']] == 'color' ? 'required' : 'nullable',
                    'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i',
                ],
            ]
        );
    }

    public function attributes(): array
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.'.$lang.'.name'] = 'name';
        }
        $this->attr['attribute_group_id'] = 'attribute';
        $this->attr['color'] = 'color';

        return $this->attr;
    }
}
