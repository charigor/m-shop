<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Models\Lang;
use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{


    private array $langArr = array();
    private array $attr = array();
    public function __construct(){

        $this->langs = Lang::whereActive(1)->get()->pluck('code');
    }
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

        foreach($this->langs as $lang){
            $this->langArr['lang.' . $lang . '.title'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string';
            $this->langArr['lang.' . $lang . '.description'] = 'nullable';
            $this->langArr['lang.' . $lang . '.meta_title'] = 'string|nullable|max:100|not_regex:~[<>{};=#]~';
            $this->langArr['lang.' . $lang . '.meta_description'] = 'string|nullable|max:200|not_regex:~[<>{};=#]~';
            $this->langArr['lang.' . $lang . '.meta_keywords'] = 'string|nullable|not_regex:~[<>{};=#]~';
            $this->langArr['lang.' . $lang . '.link_rewrite'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string|unique:category_lang,link_rewrite,'.$this->id.',id,category_id,locale';
        }
        return array_merge(
            $this->langArr,
            ['active' => 'required|integer|in:'.implode(',',array_flip(Category::ACTIVE))],
            ['description' => 'nullable'],
            ['cover_image' => 'array'],
            ['menu_thumbnail' => 'array'],
            ['parent_id' => 'integer|nullable']
        );
    }
    public function attributes()
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.' . $lang . '.title'] = 'title';
            $this->attr['lang.' . $lang . '.description'] = 'description';
            $this->attr['lang.' . $lang . '.meta_title'] = 'meta title';
            $this->attr['lang.' . $lang . '.meta_description'] = 'meta description';
            $this->attr['lang.' . $lang . '.meta_keywords'] = 'meta keywords';
            $this->attr['lang.' . $lang . '.link_rewrite'] = 'link rewrite';
        }
        $this->attr['parent_id'] = 'parent';
        return $this->attr;
    }

    public function messages(): array
    {
        return [
            'lang.'.app()->getLocale().'.title.required' => trans('validation.custom.base_lang_attr.required',['title'],app()->getLocale()),
            'lang.'.app()->getLocale().'.link_rewrite.required' => trans('validation.custom.base_lang_attr.required',['link_rewrite'],app()->getLocale())
        ];
    }
}
