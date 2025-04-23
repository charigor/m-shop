<?php

namespace App\Http\Controllers;

use App\Models\Product;
//use App\Services\Filter\SearchRepository;
use App\Services\Patterns\Adapter\Contracts\Adapter;
use App\Services\Patterns\Adapter\First;
use App\Services\Patterns\Decorator\Test\Dec\DecorationCoffeeWithChocolate;
use App\Services\Patterns\Decorator\Test\Dec\DecorationCoffeeWithMilk;
use App\Services\Patterns\Decorator\Test\Realization\Coffee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $adapter = new Adapter(new First());

        $products = Product::with(['media', 'translation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        return view('front.main', [
            'products' => $products,
        ]);
    }
}
