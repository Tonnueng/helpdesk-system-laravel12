<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
{

    public $ticket;
    public $assignedBy;

    public function __construct(Ticket $ticket, $assignedBy)
    {
        $this->ticket = $ticket;
        $this->assignedBy = $assignedBy;
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
            'assigned_by' => $this->assignedBy->name,
            'message' => 'คุณได้รับมอบหมายปัญหา #' . $this->ticket->id
        ];
    }
} 