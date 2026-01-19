<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationDateUpdated extends Notification
{
    use Queueable;

    protected $reservation;
    protected $oldDate;
    protected $newDate;

    public function __construct(Reservation $reservation, $oldDate, $newDate)
    {
        $this->reservation = $reservation;
        $this->oldDate = $oldDate;
        $this->newDate = $newDate;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $bookTitle = $this->reservation->book->title ?? 'Your reserved book';

        $mail = (new MailMessage)
            ->subject("Reservation Date Updated: {$bookTitle}")
            ->line("The expected availability date for '{$bookTitle}' has been updated.");

        // Only show old date if it exists
        if ($this->oldDate && $this->oldDate !== 'Unknown') {
            $mail->line("Previous expected date: " . $this->oldDate);
        }

        $mail->line("New expected date: " . $this->newDate)
            ->action('View My Reservations', url('/my-reservations'))
            ->line('Thank you for your patience!');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Reservation date updated for book #{$this->reservation->book_id}",
            'old_date' => $this->oldDate,
            'new_date' => $this->newDate,
        ];
    }
}