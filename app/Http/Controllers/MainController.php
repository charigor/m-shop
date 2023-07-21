<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class MainController extends Controller
{
    //

    public function index(Request $request){

        return Inertia::render('MainView', [
            'data' =>  $request->get('search') ? Product::search(
                query: trim($request->get('search')) ?? '',
            )->get() : ''
        ]);
    }

//            ->withViewData(['meta' => $event->meta]);
//        return new JsonResponse(
//            data: Product::search(
//            query: trim($request->get('search')) ?? '',
//        )->get(),
//            status: Response::HTTP_OK,
//        );
}
