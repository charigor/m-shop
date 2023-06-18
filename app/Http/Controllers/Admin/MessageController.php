<?php

namespace App\Http\Controllers\Admin;

use App\Events\StoreMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResponse;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $messages = Message::query()->orderByDesc('created_at')->get();
        return inertia('Message',[
            'messages' => MessageResponse::collection($messages)->resolve()
        ]);
    }

    public function store(Request $request)
    {

        $message = new Message();
        $message->body = $request->body;
        $message->user_id = auth()->user()->id;
        $message->save();
       broadcast(new StoreMessageEvent($message))->toOthers();
        return MessageResponse::make($message)->resolve();
    }
}
