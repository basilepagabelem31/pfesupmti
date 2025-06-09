<?php

namespace App\Notifications;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoteAdded extends Notification
{
    use Queueable;
    public $note ;

    /**
     * Create a new notification instance.
     */
    public function __construct(Note $note)
    {
       $this->note = $note ;
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


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //contenue de la notification 
            'type' => 'ajout',
            'message' => 'Une nouvelle note a été ajoutée : ' .$this->note->valeur ,
            'note_id' => $this->note->id,
            'donneur_id' => $this->note->donneur_id,
            'stagiaire_id' => $this->note->stagiaire_id,
            'date_note' => $this->note->date_note,
        ];
    }
}
