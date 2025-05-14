<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CheckoutStepOneRequest;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $deliveryTypeOptions = [
            ['id' => 1, 'text' => 'Самовивіз'],
            ['id' => 2, 'text' => 'Курьером'],
            ['id' => 3, 'text' => 'Нова Пошта'],
        ];

        return view('front.checkout.index', compact('deliveryTypeOptions'));
    }

    public function stepOne(CheckoutStepOneRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => true]);
    }
}
