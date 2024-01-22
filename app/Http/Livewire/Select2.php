<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Select2 extends Component
{
    public string $city = 'Київ';

    public array $cities = [];

    public function update()
    {
        $this->dispatch('data-city', data: $this->cities, val: $this->city);
    }

    public function getCities(): array
    {
        $response = Cache::remember('cities', 86400, function () {
            return Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => '80d7f9d73a6c4dc0c6b8ee3bd78184d6',
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
        $this->cities = $this->getCities();

        return view('livewire.select2', ['city', 'cities']);
    }
}
