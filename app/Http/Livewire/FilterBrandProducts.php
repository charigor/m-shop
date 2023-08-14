<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class FilterBrandProducts extends Component
{

    public $products,$categories_id = [],$brands_id = [],$sortBy = 'name';
    protected $queryString = ['categories_id' => ['except' => '', 'as' => 'cat']];
    public function mount($brands_id)
    {
        $this->brands_id =  [$brands_id];
    }

    public function render()
    {
        $this->products = Product::selectRaw(
            'products.*,
             product_lang.name,
             product_lang.locale,
             product_lang.product_id,
             product_lang.description,
             product_lang.meta_keywords,
             product_lang.meta_description')
            ->leftJoin('product_lang', 'product_lang.product_id', '=', 'products.id')
            ->active()
            ->when($this->brands_id,fn($q) => $q->whereHas('brand', fn($q) => $q->whereIn('brand_id',$this->brands_id)))
            ->when($this->categories_id,fn($q) =>  $q->whereHas('categories', fn($q) => $q->whereIn('category_id',$this->categories_id)))
            ->when(($this->sortBy && $this->sortBy === 'cheap'),fn($q) => $q->orderBy('price','asc'))
            ->when(($this->sortBy && $this->sortBy === 'expensive'),fn($q) => $q->orderBy('price','desc'))
            ->when(($this->sortBy && $this->sortBy === 'name'),fn($q) => $q->orderBy('name','asc'))
            ->where('locale',app()->getLocale())
            ->get();

        $sections = Category::selectRaw(
                        'categories.*,
                         category_lang.title,
                         category_lang.locale,
                         category_lang.description,
                         category_lang.meta_description,
                         category_lang.meta_keywords,
                         category_lang.meta_title,
                         category_lang.link_rewrite')
                        ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
                        ->withCount(['products' => fn($q) => $q->whereHas('brand',fn($q) => $q->whereIn('brand_id', $this->brands_id))])
                        ->where('locale',app()->getLocale())
                        ->whereNotNull('parent_id')
                        ->active()
                        ->get();

        return view('livewire.filter-brand-products',['products' => $this->products,'sections' => $sections]);
    }
}
