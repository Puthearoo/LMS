<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BookReservedNotification extends Notification
{
    use Queueable;

    protected $reservation;
    protected $expectedDate;
    protected $queuePosition;

    // Make the constructor more flexible
    public function __construct($reservation, $expectedDate, $queuePosition)
    {
        $this->reservation = $reservation;
        $this->expectedDate = $expectedDate;
        $this->queuePosition = $queuePosition;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Safely get book title
        $bookTitle = 'a book';
        if (isset($this->reservation->book) && isset($this->reservation->book->title)) {
            $bookTitle = $this->reservation->book->title;
        } elseif (is_object($this->reservation) && property_exists($this->reservation, 'book')) {
            $bookTitle = $this->reservation->book->title ?? 'a book';
        }

        return (new MailMessage)
            ->subject("Reservation Confirmed: {$bookTitle}")
            ->line("You've successfully reserved '{$bookTitle}'")
            ->line("Your position in queue: #{$this->queuePosition}")
            ->line("Expected available by: {$this->expectedDate}")
            ->action('View My Reservations', url('/my-reservations'))
            ->line('Thank you for using our library!');
    }

    public function toArray($notifiable)
    {
        $bookId = 'unknown';
        if (isset($this->reservation->book_id)) {
            $bookId = $this->reservation->book_id;
        } elseif (is_object($this->reservation) && property_exists($this->reservation, 'book_id')) {
            $bookId = $this->reservation->book_id;
        }

        return [
            'message' => "Reservation confirmed for book #{$bookId}",
            'queue_position' => $this->queuePosition,
        ];
    }
}