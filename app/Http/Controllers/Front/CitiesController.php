<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        $apiKey = config('services.nova_poshta.api_key');
        $response = Http::post("https://api.novaposhta.ua/v2.0/json/", [
            'apiKey' => config('services.nova_poshta.api_key'),
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlements',
            'methodProperties' => [
                'CityName' => $request->input('term'),
                'Page' => $request->input('page', '1'),
                'Limit' => 30,
            ]
        ]);

        return $response->json();
    }
}
