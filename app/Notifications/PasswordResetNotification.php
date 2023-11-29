<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->data;
        return (new MailMessage)
                ->greeting('Hello '.$data['name'].'!')
                ->subject("Reset Password Notification!")
                ->line("You are receiving this email because we received a password reset request for your ". env('APP_NAME') ." account on ". \Carbon\Carbon::now())
                ->line('Click on the button below to reset your password.')
                ->action('Reset Password', url('/auth/reset-password/'.$data['verification_code'].'?email='. $data['email']))
                ->line('If you did not request for a password reset on your '. env('APP_NAME') .' account, please disregard this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
