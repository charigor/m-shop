<?php

namespace App\Http\Controllers;

use App\Models\Product;
//use App\Services\Filter\SearchRepository;
use App\Services\Patterns\AbstractFactory\Factories\PaypalGatewayFactory;
use App\Services\Patterns\Builder\MysqlBuilder;
use App\Services\Patterns\FabricMethod\Fabrics\SmsFactory;
use App\Services\Patterns\FabricMethod\Fabrics\TelegramFactory;
use App\Services\Patterns\FabricMethod\SmsChannel;
use App\Services\Patterns\FabricMethod\Test\Factories\EmailFactory;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $notification = (EmailFactory::class)->make();
//        dd($notification->send('Wow'));

        $products = Product::with(['media', 'translation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        return view('front.main', [
            'products' => $products,
        ]);
    }
}
