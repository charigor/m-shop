<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Payment;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{

//    public function __construct(protected Payment $payment){
//
////        dd($this->payment->pay());
//    }
    /**
     * @return \Inertia\Response
     */
    public function index()
    {

//        dd($this->payment->pay());
//        return Inertia::render('Users/Index', [
//            'users' => User::query()->get(),
//        ]);
    }

    public function pay(Request $request){

//        $post = User::whereHas('comments.post',function  ($query) {
//            $query->where('id','5');
//        })->get();
//
//        Comment::whereHas('commentable.tags',function($query){
//                $query->where('title','funny');
//        });
    }
}



