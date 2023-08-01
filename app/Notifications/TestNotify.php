<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotify extends Notification implements ShouldQueue
{
    use Queueable;
    private array $enrollmentData;
    /**
     * Create a new notification instance.
     */
    public function __construct(array $enrollmentData)
    {
        $this->enrollmentData = $enrollmentData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

//    /**
//     * Get the mail representation of the notification.
//     */
//    public function toMail(object $notifiable): MailMessage
//    {
//        return (new MailMessage)
//                    ->line($this->enrollmentData['body'])
//                    ->action($this->enrollmentData['text'], $this->enrollmentData['url'])
//                    ->line($this->enrollmentData['thanks']);
//    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'name' => 'Igor',
            'message' => $this->enrollmentData['body'],
        ];
    }
}
