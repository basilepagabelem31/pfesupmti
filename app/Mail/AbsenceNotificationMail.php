<?php

namespace App\Mail;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbsenceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $absence;

    /**
     * Create a new message instance.
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Notification d\'absence à la réunion')
                    ->view('emails.absence_notification');
    }
}