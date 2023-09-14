<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class Checkout extends Component
{
    public $fullname = null,$email=null;
    public function mount()
    {

    }
    public function render()
    {
        info($this->fullname);
        return view('livewire.checkout');
    }
}
