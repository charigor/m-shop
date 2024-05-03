<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NovaPoshtaController extends Controller
{
    private array $warehouseTypes = [
        'Поштомат',
        'Поштове відділення'
    ];

    public function getCities(Request $request)
    {
        $response = Http::post("https://api.novaposhta.ua/v2.0/json/", [
            'apiKey' => config('services.nova_poshta.api_key'),
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlements',
            'methodProperties' => [
                'CityName' => $request->input('q'),
                'Page' => $request->input('page', 1),
                'Limit' => $request->input('limit', 10),
            ]
        ]);
        return $response->json();
    }

    public function getWarehouses(Request $request)
    {
        $response = Http::post('https://api.novaposhta.ua/v2.0/json/', [
            'apiKey' => config('services.nova_poshta.api_key'),
            'modelName' => 'Address',
            'calledMethod' => 'getWarehouses',
            'methodProperties' => [
                'SettlementRef' => $request->get('SettlementRef'),
                'TypeOfWarehouseRef' => $request->get('TypeOfWarehouseRef'),
                'FindByString' => $request->input('q'),
                'Limit' => $request->input('limit', 10),
                'Page' => $request->input('page',1)
            ],
        ]);

        return $response->json();
    }

    public function getWarehouseTypes(Request $request)
    {
        $response =   Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => config('services.nova_poshta.api_key'),
                'modelName' => 'Address',
                'calledMethod' => 'getWarehouseTypes',
                'methodProperties' => null
            ]);



        $content = json_decode($response->getBody());

        return response()->json(collect($content->data)->filter(fn($item) => in_array($item->Description, $this->warehouseTypes))->flatten());

    }
}
