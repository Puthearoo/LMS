<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationDateChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $oldDate;
    public $newDate;

    public function __construct(Reservation $reservation, $oldDate, $newDate)
    {
        $this->reservation = $reservation;
        $this->oldDate = $oldDate;
        $this->newDate = $newDate;
    }

    public function build()
    {
        return $this->subject('Library Reservation Date Updated')
            ->view('emails.reservation-date-changed');
    }
}