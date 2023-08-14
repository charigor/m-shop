<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->whereHas('translation')->paginate();
        return  view('front.brand.index',compact('brands'));
    }

    public function show(Brand $brand)
    {
//        $products = Product::active()->where('brand_id',$brand->id)->get();

        return  view('front.brand.show',compact('brand'));
    }
}
