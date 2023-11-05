<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FormSelect extends Component
{
    public $item;

    public $selected = null;

    public $label;

    public $open = false;

    public function toggle()
    {
        $this->open = ! $this->open;
    }

    public function render()
    {
        return view('livewire.form-select', ['item' => $this->item, 'selected' => $this->selected, 'label' => $this->label, 'open' => $this->open]);
    }
}
