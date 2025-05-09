<?php

namespace App\Http\Livewire;

use Spatie\LivewireWizard\Components\StepComponent;

class CheckoutStepComponent extends StepComponent
{
    public ?string $name = null;

    public ?string $email = null;

    public function mount()
    {

        $this->name = auth()->check() ? auth()->user()->name : null;
        $this->email = auth()->check() ? auth()->user()->email : null;
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:16',
            'email' => ['required', 'email', 'not_in:'.auth()->user()->email],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function stepInfo(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function render()
    {
        return view('livewire.checkout-step', ['name' => $this->name, 'email']);
    }
}
