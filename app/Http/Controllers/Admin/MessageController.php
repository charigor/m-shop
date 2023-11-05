<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResponse;
use App\Models\Message;
use App\Models\User;
use App\Notifications\TestNotify;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    /**
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        //
        //        $enrollmentData = [
        //            'body' => 'some body',
        //            'text' => 'some text',
        //            'name' => 'igor',
        //            'url' => url('/'),
        //            'thanks' => 'thanks',
        //        ];
        //        $user = User::first();
        //        Notification::send(auth()->user(), new TestNotify($enrollmentData));
        $messages = Message::query()->orderByDesc('created_at')->get();

        return inertia('Message', [
            'messages' => MessageResponse::collection($messages)->resolve(),
        ]);
    }

    public function store(Request $request)
    {

        $message = new Message();
        $message->body = $request->body;
        $message->user_id = auth()->user()->id;
        $message->save();

        return MessageResponse::make($message)->resolve();
    }

    public function getNotify(Request $request)
    {
        return response()->json(['messages' => DatabaseNotification::where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
            ->orderBy('read_at', 'asc')
            ->orderBy('created_at', 'desc')->get()]);
    }

    public function markRead(Request $request)
    {
        $notification = auth()->user()->notifications()->find($request->id);
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['messages' => DatabaseNotification::where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
            ->orderBy('read_at', 'asc')
            ->orderBy('created_at', 'desc')->get()]);
    }
}
