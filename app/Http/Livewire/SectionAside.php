<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class SectionAside extends Component
{

    public $products;

    public function mount($products)
    {
        $this->products = $products;
    }


    public function render()
    {
        $this->products = Product::when($this->selected,function($q) {
            $q->whereHas('categories', function($query)  {$query->whereIn('category_id',$this->selected);});
        })->get();
        return view('livewire.section-aside',['products' => $this->products]);
    }
}
