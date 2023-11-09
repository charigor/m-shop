<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;
use Maski\Maski\MaskiFacade;

class BrandCreateRequest extends FormRequest
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
            $this->langArr['lang.'.$lang.'.short_description'] = 'nullable';
            $this->langArr['lang.'.$lang.'.description'] = 'nullable';
            $this->langArr['lang.'.$lang.'.meta_title'] = 'string|nullable|max:100|not_regex:~[<>{};=#]~';
            $this->langArr['lang.'.$lang.'.meta_description'] = 'string|nullable|max:200|not_regex:~[<>{};=#]~';
            $this->langArr['lang.'.$lang.'.meta_keywords'] = 'string|nullable|not_regex:~[<>{};=#]~';
        }

        return array_merge(
            $this->langArr,
            ['phone' => 'required|phone:ua'],
            ['name' => 'required|string'],
            ['active' => 'required|integer|in:'.implode(',', array_flip(Brand::ACTIVE))],
            ['short_description' => 'nullable'],
            ['description' => 'nullable'],
            ['image' => 'array'],

        );
    }

    public function attributes(): array
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.'.$lang.'.short_description'] = 'short_description';
            $this->attr['lang.'.$lang.'.description'] = 'description';
            $this->attr['lang.'.$lang.'.meta_title'] = 'meta title';
            $this->attr['lang.'.$lang.'.meta_description'] = 'meta description';
            $this->attr['lang.'.$lang.'.meta_keywords'] = 'meta keywords';
        }
        $this->attr['name'] = 'name';
        $this->attr['phone'] = 'phone';

        return $this->attr;
    }
}
