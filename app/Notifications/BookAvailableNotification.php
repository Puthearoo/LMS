<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BookAvailableNotification extends Notification
{
    use Queueable;

    protected $reservation;
    protected $pickupDeadline;

    public function __construct(Reservation $reservation, $pickupDeadline = null)
    {
        $this->reservation = $reservation;
        $this->pickupDeadline = $pickupDeadline;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $bookTitle = $this->reservation->book->title ?? 'a book';
        $mailMessage = (new MailMessage)
            ->subject("Book Available: {$bookTitle}")
            ->line("Great news! '{$bookTitle}' is now available for pickup!")
            ->line("Your reservation is ready for collection.");

        if ($this->pickupDeadline) {
            $mailMessage->line("Please pick it up by: {$this->pickupDeadline}");
        }

        return $mailMessage
            ->action('Pick Up Book', url('/reservations/' . $this->reservation->id))
            ->line("If you don't pick it up within 3 days, it may be offered to the next person in line.");
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Book #{$this->reservation->book_id} is now available",
            'pickup_deadline' => $this->pickupDeadline,
        ];
    }
}