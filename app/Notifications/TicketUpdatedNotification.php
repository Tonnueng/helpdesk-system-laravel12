<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketUpdate;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification
{

    public $ticket;
    public $update;

    public function __construct(Ticket $ticket, TicketUpdate $update)
    {
        $this->ticket = $ticket;
        $this->update = $update;
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
            'update_id' => $this->update->id,
            'updated_by' => $this->update->user->name,
            'status' => $this->ticket->status->name ?? null,
            'assigned_to' => $this->ticket->assignedTo->name ?? null,
            'comment' => $this->update->comment,
            'message' => 'มีการอัปเดตปัญหา #' . $this->ticket->id
        ];
    }
} 