<?php

namespace App\Http\Controllers;

use App\Models\Product;
//use App\Services\Filter\SearchRepository;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['media', 'translation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        return view('front.main', [
            'products' => $products,
        ]);
    }
}
