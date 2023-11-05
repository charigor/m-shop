<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\ProductTypeEnum;
use App\Models\Lang;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProductCreateRequest extends FormRequest
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

            $this->langArr['lang.'.$lang.'.name'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string||max:128';
            $this->langArr['lang.'.$lang.'.short_description'] = 'nullable';
            $this->langArr['lang.'.$lang.'.description'] = 'nullable';
            $this->langArr['lang.'.$lang.'.meta_title'] = 'string|nullable|max:100|not_regex:~[<>{};=#]~|max:128';
            $this->langArr['lang.'.$lang.'.meta_description'] = 'string|nullable|max:200|not_regex:~[<>{};=#]~|max:512';
            $this->langArr['lang.'.$lang.'.meta_keywords'] = 'string|nullable|not_regex:~[<>{};=#]~|max:255';
            $this->langArr['lang.'.$lang.'.link_rewrite'] = app()->getLocale() === $lang ? 'required|' : 'nullable|'.'string|unique:product_lang,link_rewrite,product_id,locale';
        }

        return array_merge(
            $this->langArr,
            ['active' => 'required|integer|in:'.implode(',', array_flip(Product::ACTIVE))],
            ['image' => 'array'],
            ['quantity' => 'integer|min:0'],
            ['price' => 'numeric|min:0'],
            ['rebate' => 'numeric|min:0'],
            ['reference' => 'string|nullable'],
            ['categories' => 'array'],
            ['main_image' => 'string|nullable'],
            ['width' => 'numeric|min:0|max:1000'],
            ['height' => 'numeric|min:0|max:1000'],
            ['depth' => 'numeric|min:0|max:1000'],
            ['weight' => 'numeric|min:0|max:1000'],
            ['unity' => 'string|nullable'],
            ['unit_price_ratio' => 'numeric|min:0|nullable'],
            ['tax_id' => 'integer|nullable'],
            ['brand_id' => 'integer|nullable'],
            ['type' => [new Enum(ProductTypeEnum::class)]],
            ['features' => 'array'],
            ['features*.feature_id' => 'integer'],
            ['features*.feature_value_id' => 'integer'],
            ['attributes' => 'array']
        );
    }

    public function attributes(): array
    {
        foreach ($this->langs as $lang) {
            $this->attr['lang.'.$lang.'.name'] = 'name';
            $this->attr['lang.'.$lang.'.short_description'] = 'short description';
            $this->attr['lang.'.$lang.'.description'] = 'description';
            $this->attr['lang.'.$lang.'.meta_title'] = 'meta title';
            $this->attr['lang.'.$lang.'.meta_description'] = 'meta description';
            $this->attr['lang.'.$lang.'.meta_keywords'] = 'meta keywords';
            $this->attr['lang.'.$lang.'.link_rewrite'] = 'link rewrite';
        }
        $this->attr['brand_id'] = 'brand';
        $this->attr['tax_id'] = 'tax';
        $this->attr['unit_price_ratio'] = 'unit price ratio';
        $this->attr['features.*.feature_id'] = 'feature';
        $this->attr['features.*.feature_value_id'] = 'feature value';

        return $this->attr;
    }

    public function messages(): array
    {
        return [
            'lang.'.app()->getLocale().'.title.required' => trans('validation.custom.base_lang_attr.required', ['name'], app()->getLocale()),
            'lang.'.app()->getLocale().'.link_rewrite.required' => trans('validation.custom.base_lang_attr.required', ['link_rewrite'], app()->getLocale()),
        ];
    }
}
