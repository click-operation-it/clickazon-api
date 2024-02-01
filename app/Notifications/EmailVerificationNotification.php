<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
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
        $data = $this->data;
        $url = env('CUSTOMER_BASE_URL') . '#/auth/verify-email/' . $data['verification_code'] . '?email=' . $data['email'];
        return (new MailMessage)
                ->greeting('Hello ' . $data['firstname'] . '!')
                ->line('Thank you for registering with ' . env('APP_NAME') . '.')
                ->line('To verify your email address, please click on the button below:')
                ->action('Verify Email', $url)
                ->line('If you did not create an account with ' . env('APP_NAME') . ', please disregard this email.')
                ->line('We are excited to have you as a part of our community!')
                ->line('By verifying your email, you gain access to exclusive features and updates.')
                ->line('If you have any questions or need assistance, feel free to reach out to our support team.')
                ->line('Thank you again for choosing ' . env('APP_NAME') . '.');

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
