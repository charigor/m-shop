<?php

namespace App\Http\Requests\Admin;

use App\Models\AttributeGroup;
use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;

class AttributeGroupCreateRequest extends FormRequest
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
            $this->langArr['lang.'.$lang.'.public_name'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
        }

        return array_merge(
            $this->langArr,
            ['group_type' => 'required|integer|in:'.implode(',', array_flip(AttributeGroup::GROUP_TYPE))]
        );
    }

    public function attributes(): array
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.'.$lang.'.name'] = 'name';
            $this->attr['lang.'.$lang.'.public_name'] = 'public_name';
        }
        $this->attr['group_type'] = 'group_type';

        return $this->attr;
    }
}
