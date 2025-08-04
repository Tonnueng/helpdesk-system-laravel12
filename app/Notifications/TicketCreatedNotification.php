<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database'];
    }



    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'category' => $this->ticket->category->name,
            'priority' => $this->ticket->priority->name,
            'reporter' => $this->ticket->user->name,
            'message' => 'มีปัญหาใหม่ #' . $this->ticket->id . ' ที่ต้องการความช่วยเหลือ'
        ];
    }
} 