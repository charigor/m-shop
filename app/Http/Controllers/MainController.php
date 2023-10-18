<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use App\Notifications\TestNotify;
//use App\Services\Filter\SearchRepository;
use App\Services\Filter\ElasticSearchRepository;
use App\Services\Test\TestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::with(['media', 'translation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();
        return view('front.main', [
            'products' => $products
        ]);
    }
}
