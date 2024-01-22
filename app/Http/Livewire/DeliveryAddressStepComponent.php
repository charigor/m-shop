<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LivewireWizard\Components\StepComponent;

class DeliveryAddressStepComponent extends StepComponent
{
    public array $cities = [];

    public string $name = '';

    public string $street = '';

    public string $zip = '';

    public string $city = 'Київ';

    public array $rules = [
        'name' => 'required',
        'street' => 'required',
        'zip' => 'required',
        'city' => 'required',
    ];

    public function submit()
    {
        $this->validate();

        $this->nextStep();
    }

    public function mount(): void
    {
        $this->cities = $this->getCities();
    }

    public function getCities(): array
    {
        $response = Cache::remember('cities', 86400, function () {
            return Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => '013ede894a12c304faf7f1f46b44c17d',
                'modelName' => 'Address',
                'calledMethod' => 'getCities',
                'methodProperties' => [
                    'Limit' => '100000',
                ],
            ])->body();
        });

        $response = json_decode($response, true);

        $collect = collect($response['data']);

        $locale = app()->getLocale();

        if ($locale == 'uk') {
            $collect = $collect->unique('Description')->pluck('Description');
        } else {
            $collect = $collect->unique('Description')->pluck('Description');
        }

        return $collect->toArray();

        //        return response()->json([
        //            'status' => 'ok',
        //            'code' => 200,
        //            'count' => $collect->count(),
        //            'data' => ['items' => $collect],
        //        ]);
    }

    public function render()
    {
        return view('livewire.checkout-delivery-step');
    }
}
