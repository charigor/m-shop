<?php

namespace App\Notifications;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;


class TestNotify extends Notification implements ShouldBroadcast
{
    private array $enrollmentData;
    /**
     * Create a new notification instance.
     */
    public function __construct(array $enrollmentData)
    {
        $this->enrollmentData = $enrollmentData;
    }

    public function via($notifiable): array
    {
        return ['broadcast'];
    }


//    public function broadcastOn(): array
//    {
//        return [
//            new Channel('store_message'),
//        ];
//    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->enrollmentData['body']. "Message for ".$this->enrollmentData['name']
        ]);
    }
}
