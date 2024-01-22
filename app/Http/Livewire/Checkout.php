<?php

namespace App\Http\Livewire;

use App\Services\Wizard\OrderWizardState;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;

class Checkout extends Component
{
    public array $cities = [];

    public array $warehouses = [];

    public array $typeOptions = [
        [
            'id' => 1,
            'name' => 'Нова пошта - відділення',
            'value' => '9a68df70-0267-42a8-bb5c-37f427e36ee4',
        ],
        [
            'id' => 2,
            'name' => 'Нова пошта поштомат',
            'value' => 'f9316480-5f2d-425d-bc2c-ac7cd29decf0',
        ],
        [
            'id' => 3,
            'name' => 'Нова пошта до дверей',
            'value' => '',
        ],
        [
            'id' => 4,
            'name' => 'Укрпошта',
        ],
        [
            'id' => 5,
            'name' => 'Самовивіз',
        ],
    ];

    public string $type = '';

    public string $city = '';

    public string $warehouse = '';
    //    public function steps(): array
    //    {
    //        return [
    //            CheckoutStepComponent::class,
    //            DeliveryAddressStepComponent::class,
    //            PaymentOrderStepComponent::class,
    //        ];
    //    }
    //    public function stateClass(): string
    //    {
    //        return OrderWizardState::class;
    //    }

    #[NoReturn]
    public function updatedCity($city): void
    {
        $this->warehouse = '';
        $this->getWarehouses($city);
    }

    #[NoReturn]
    public function updatedWarehouse($value): void
    {
        $this->warehouse = $value;
    }

    #[NoReturn]
    public function updatedType($value): void
    {
        $this->warehouse = '';
        $this->type = $value;
    }

    public function mount()
    {
        $this->getCities($this->city);
        $this->getWarehouses($this->city);
    }

    /**
     * @return void
     */
    public function getCities($term)
    {
        $response = Cache::remember('cities', 86400, function () {
            return Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => '013ede894a12c304faf7f1f46b44c17d',
                'modelName' => 'Address',
                'calledMethod' => 'getCities',
                'methodProperties' => [
                    'Limit' => 10000,
                ],
            ])->body();
        });
        $response = json_decode($response, true);

        $collect = collect($response['data']);

        $locale = app()->getLocale();

        $collect = $collect->unique('Description')->filter(function ($item) use ($term) {
            return strpos(mb_strtolower($item['Description']), mb_strtolower($term)) !== false;
        })->pluck('Description');

        $this->cities = $collect->toArray() ?? [];
    }

    public function getWarehouses($city): void
    {

        $response = Cache::remember('warehouses_'.$city.'_'.$this->type, 86400, function () use ($city) {
            return Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => '013ede894a12c304faf7f1f46b44c17d',
                'modelName' => 'Address',
                'calledMethod' => 'getWarehouses',
                'methodProperties' => [
                    'TypeOfWarehouseRef' => 'f9316480-5f2d-425d-bc2c-ac7cd29decf0',
                    'CityName' => $city,
                ],
            ])->body();
        });

        $response = json_decode($response, true);

        $collect = collect($response['data']);
        $locale = app()->getLocale();

        $collect = $collect->unique('Description')->pluck('Description');

        $this->warehouses = $collect->toArray();
    }

    public function render()
    {
        $this->dispatchBrowserEvent('updated-data');

        return view('livewire.checkout', ['city', 'cities', 'type', 'warehouses', 'warehouse']);
    }
}
