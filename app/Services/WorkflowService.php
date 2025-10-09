<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\TicketWorkflow;
use App\Models\TicketUpdate;
use App\Models\User;
use App\Models\Status;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketUpdatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkflowService
{
    /**
     * เริ่มต้น workflow สำหรับ ticket
     */
    public function startWorkflow(Ticket $ticket, Workflow $workflow = null)
    {
        try {
            // ถ้าไม่ได้ระบุ workflow ให้หา workflow ที่เหมาะสม
            if (!$workflow) {
                $workflow = $this->findApplicableWorkflow($ticket);
            }

            if (!$workflow) {
                Log::info("No applicable workflow found for ticket {$ticket->id}");
                return false;
            }

            // ตรวจสอบว่า ticket นี้มี workflow ที่กำลังทำงานอยู่หรือไม่
            $existingWorkflow = TicketWorkflow::where('ticket_id', $ticket->id)
                ->where('status', 'running')
                ->first();

            if ($existingWorkflow) {
                Log::info("Ticket {$ticket->id} already has a running workflow");
                return false;
            }

            // สร้าง ticket workflow
            $ticketWorkflow = TicketWorkflow::create([
                'ticket_id' => $ticket->id,
                'workflow_id' => $workflow->id,
                'status' => 'running',
                'started_at' => now(),
                'next_action_at' => now(),
            ]);

            Log::info("Started workflow {$workflow->name} for ticket {$ticket->id}");

            // เริ่มดำเนินการขั้นตอนแรก
            $this->executeNextStep($ticketWorkflow);

            return $ticketWorkflow;

        } catch (\Exception $e) {
            Log::error("Failed to start workflow for ticket {$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * หา workflow ที่เหมาะสมสำหรับ ticket
     */
    public function findApplicableWorkflow(Ticket $ticket)
    {
        $workflows = Workflow::active()->ordered()->get();

        foreach ($workflows as $workflow) {
            if ($workflow->canTrigger($ticket)) {
                return $workflow;
            }
        }

        return null;
    }

    /**
     * ดำเนินการขั้นตอนถัดไปของ workflow
     */
    public function executeNextStep(TicketWorkflow $ticketWorkflow)
    {
        try {
            if (!$ticketWorkflow->isRunning()) {
                return false;
            }

            $currentStep = $ticketWorkflow->currentStep;
            
            // ถ้ายังไม่มี current step ให้หา step แรก
            if (!$currentStep) {
                $currentStep = $this->getNextStep($ticketWorkflow);
                if ($currentStep) {
                    $ticketWorkflow->update(['current_step_id' => $currentStep->id]);
                }
            }

            if (!$currentStep) {
                // ไม่มี step ถัดไป - workflow เสร็จสิ้น
                $this->completeWorkflow($ticketWorkflow);
                return true;
            }

            // ตรวจสอบว่าขั้นตอนนี้พร้อมดำเนินการหรือไม่
            if (!$currentStep->shouldExecute($ticketWorkflow)) {
                return false;
            }

            // ดำเนินการขั้นตอน
            $result = $this->executeStep($ticketWorkflow, $currentStep);

            if ($result) {
                // ทำเครื่องหมายขั้นตอนเป็นเสร็จสิ้น
                $ticketWorkflow->markStepCompleted($currentStep->id);
                
                // ย้ายไปขั้นตอนถัดไป
                $this->moveToNextStep($ticketWorkflow);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Failed to execute workflow step for ticket workflow {$ticketWorkflow->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดำเนินการขั้นตอนเฉพาะ
     */
    public function executeStep(TicketWorkflow $ticketWorkflow, WorkflowStep $step)
    {
        try {
            $ticket = $ticketWorkflow->ticket;
            $config = $step->action_config ?? [];

            switch ($step->action_type) {
                case 'assign':
                    return $this->executeAssignAction($ticket, $config);
                    
                case 'status_change':
                    return $this->executeStatusChangeAction($ticket, $config);
                    
                case 'notification':
                    return $this->executeNotificationAction($ticket, $config);
                    
                case 'auto_reply':
                    return $this->executeAutoReplyAction($ticket, $config);
                    
                case 'escalation':
                    return $this->executeEscalationAction($ticket, $config);
                    
                case 'email_notification':
                    return $this->executeEmailNotificationAction($ticket, $config);
                    
                case 'set_due_date':
                    return $this->executeSetDueDateAction($ticket, $config);
                    
                case 'add_comment':
                    return $this->executeAddCommentAction($ticket, $config);
                    
                case 'wait_for_response':
                    return $this->executeWaitForResponseAction($ticketWorkflow, $config);
                    
                default:
                    Log::warning("Unknown workflow step action type: {$step->action_type}");
                    return true; // ข้ามขั้นตอนที่ไม่รู้จัก
            }

        } catch (\Exception $e) {
            Log::error("Failed to execute step {$step->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * มอบหมายงาน
     */
    private function executeAssignAction(Ticket $ticket, array $config)
    {
        if (!isset($config['user_id'])) {
            return false;
        }

        $user = User::find($config['user_id']);
        if (!$user) {
            return false;
        }

        $ticket->update(['assigned_to_user_id' => $user->id]);
        
        // ส่งการแจ้งเตือน
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first() ?? User::first();
        $user->notify(new TicketAssignedNotification($ticket, $systemUser));

        Log::info("Assigned ticket {$ticket->id} to user {$user->name}");
        return true;
    }

    /**
     * เปลี่ยนสถานะ
     */
    private function executeStatusChangeAction(Ticket $ticket, array $config)
    {
        if (!isset($config['status_id'])) {
            return false;
        }

        $status = Status::find($config['status_id']);
        if (!$status) {
            return false;
        }

        $ticket->update(['status_id' => $status->id]);
        
        // สร้าง TicketUpdate record
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first() ?? User::first();
        $update = TicketUpdate::create([
            'ticket_id' => $ticket->id,
            'user_id' => $systemUser->id,
            'comment' => "สถานะเปลี่ยนเป็น: {$status->name}",
            'type' => 'status_change'
        ]);
        
        // ส่งการแจ้งเตือน
        $ticket->user->notify(new TicketUpdatedNotification($ticket, $update));

        Log::info("Changed status of ticket {$ticket->id} to {$status->name}");
        return true;
    }

    /**
     * ส่งการแจ้งเตือน
     */
    private function executeNotificationAction(Ticket $ticket, array $config)
    {
        $message = $config['message'] ?? 'มีการอัปเดตปัญหา';
        
        // สร้าง TicketUpdate record
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first() ?? User::first();
        $update = TicketUpdate::create([
            'ticket_id' => $ticket->id,
            'user_id' => $systemUser->id,
            'comment' => $message,
            'type' => 'notification'
        ]);
        
        // ส่งการแจ้งเตือนให้ผู้แจ้งปัญหา
        $ticket->user->notify(new TicketUpdatedNotification($ticket, $update));
        
        // ส่งการแจ้งเตือนให้ผู้รับผิดชอบ (ถ้ามี)
        if ($ticket->assignedTo) {
            $ticket->assignedTo->notify(new TicketUpdatedNotification($ticket, $update));
        }

        Log::info("Sent notification for ticket {$ticket->id}: {$message}");
        return true;
    }

    /**
     * ตอบกลับอัตโนมัติ
     */
    private function executeAutoReplyAction(Ticket $ticket, array $config)
    {
        $message = $config['message'] ?? 'ขอบคุณสำหรับการแจ้งปัญหา เราจะดำเนินการแก้ไขโดยเร็วที่สุด';
        
        // หา system user (CEO หรือ Admin)
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first();
        if (!$systemUser) {
            $systemUser = User::first(); // fallback
        }
        
        $ticket->updates()->create([
            'user_id' => $systemUser->id,
            'comment' => $message,
        ]);

        Log::info("Added auto reply to ticket {$ticket->id}");
        return true;
    }

    /**
     * ส่งต่อให้ผู้จัดการ
     */
    private function executeEscalationAction(Ticket $ticket, array $config)
    {
        $managers = User::whereIn('role', ['manager', 'ceo'])->get();
        
        // สร้าง TicketUpdate record
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first() ?? User::first();
        $update = TicketUpdate::create([
            'ticket_id' => $ticket->id,
            'user_id' => $systemUser->id,
            'comment' => "ปัญหาได้รับการส่งต่อเนื่องจากความสำคัญ",
            'type' => 'escalation'
        ]);
        
        foreach ($managers as $manager) {
            $manager->notify(new TicketUpdatedNotification($ticket, $update));
        }

        Log::info("Escalated ticket {$ticket->id} to managers");
        return true;
    }

    /**
     * ส่งอีเมลแจ้งเตือน
     */
    private function executeEmailNotificationAction(Ticket $ticket, array $config)
    {
        $subject = $config['subject'] ?? 'อัปเดตปัญหา';
        $message = $config['message'] ?? 'มีการอัปเดตปัญหา';
        
        // ส่งอีเมลให้ผู้แจ้งปัญหา
        Mail::send([], [], function ($mail) use ($ticket, $subject, $message) {
            $mail->to($ticket->user->email)
                 ->subject($subject)
                 ->setBody($message, 'text/html');
        });

        Log::info("Sent email notification for ticket {$ticket->id}");
        return true;
    }

    /**
     * กำหนดวันครบกำหนด
     */
    private function executeSetDueDateAction(Ticket $ticket, array $config)
    {
        $hours = $config['hours'] ?? 48;
        $dueDate = now()->addHours($hours);
        
        // หา system user (CEO หรือ Admin)
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first();
        if (!$systemUser) {
            $systemUser = User::first(); // fallback
        }
        
        // เพิ่มความคิดเห็นเกี่ยวกับวันครบกำหนด
        $ticket->updates()->create([
            'user_id' => $systemUser->id,
            'comment' => "กำหนดวันครบกำหนด: {$dueDate->format('d/m/Y H:i')}",
        ]);

        Log::info("Set due date for ticket {$ticket->id} to {$dueDate}");
        return true;
    }

    /**
     * เพิ่มความคิดเห็น
     */
    private function executeAddCommentAction(Ticket $ticket, array $config)
    {
        $comment = $config['comment'] ?? 'ความคิดเห็นจากระบบ';
        
        // หา system user (CEO หรือ Admin)
        $systemUser = User::whereIn('role', ['ceo', 'manager'])->first();
        if (!$systemUser) {
            $systemUser = User::first(); // fallback
        }
        
        $ticket->updates()->create([
            'user_id' => $systemUser->id,
            'comment' => $comment,
        ]);

        Log::info("Added comment to ticket {$ticket->id}");
        return true;
    }

    /**
     * รอการตอบกลับ
     */
    private function executeWaitForResponseAction(TicketWorkflow $ticketWorkflow, array $config)
    {
        $hours = $config['hours'] ?? 24;
        $nextActionAt = now()->addHours($hours);
        
        $ticketWorkflow->update(['next_action_at' => $nextActionAt]);
        
        Log::info("Set wait time for ticket workflow {$ticketWorkflow->id} until {$nextActionAt}");
        return true;
    }

    /**
     * ได้รับขั้นตอนถัดไป
     */
    private function getNextStep(TicketWorkflow $ticketWorkflow)
    {
        $completedSteps = $ticketWorkflow->completed_steps ?? [];
        
        return $ticketWorkflow->workflow->steps()
            ->whereNotIn('id', $completedSteps)
            ->orderBy('step_order')
            ->first();
    }

    /**
     * ย้ายไปขั้นตอนถัดไป
     */
    private function moveToNextStep(TicketWorkflow $ticketWorkflow)
    {
        $nextStep = $this->getNextStep($ticketWorkflow);
        
        if ($nextStep) {
            $nextActionAt = $this->calculateNextActionTime($nextStep);
            
            $ticketWorkflow->update([
                'current_step_id' => $nextStep->id,
                'next_action_at' => $nextActionAt,
            ]);
        } else {
            // ไม่มีขั้นตอนถัดไป - workflow เสร็จสิ้น
            $this->completeWorkflow($ticketWorkflow);
        }
    }

    /**
     * คำนวณเวลาดำเนินการถัดไป
     */
    private function calculateNextActionTime(WorkflowStep $step)
    {
        $conditions = $step->conditions ?? [];
        
        if (isset($conditions['time_delay'])) {
            $delayMinutes = $conditions['time_delay'];
            return now()->addMinutes($delayMinutes);
        }
        
        return now();
    }

    /**
     * เสร็จสิ้น workflow
     */
    private function completeWorkflow(TicketWorkflow $ticketWorkflow)
    {
        $ticketWorkflow->update([
            'status' => 'completed',
            'completed_at' => now(),
            'current_step_id' => null,
            'next_action_at' => null,
        ]);

        Log::info("Completed workflow for ticket {$ticketWorkflow->ticket_id}");
    }

    /**
     * ประมวลผล workflow ทั้งหมดที่พร้อมดำเนินการ
     */
    public function processDueWorkflows()
    {
        $dueWorkflows = TicketWorkflow::dueForAction()->with(['ticket', 'currentStep', 'workflow'])->get();
        
        foreach ($dueWorkflows as $ticketWorkflow) {
            $this->executeNextStep($ticketWorkflow);
        }
        
        Log::info("Processed {$dueWorkflows->count()} due workflows");
    }

    /**
     * หยุด workflow
     */
    public function pauseWorkflow(TicketWorkflow $ticketWorkflow)
    {
        $ticketWorkflow->update(['status' => 'paused']);
        Log::info("Paused workflow for ticket {$ticketWorkflow->ticket_id}");
    }

    /**
     * เริ่มต้น workflow ใหม่
     */
    public function resumeWorkflow(TicketWorkflow $ticketWorkflow)
    {
        $ticketWorkflow->update([
            'status' => 'running',
            'next_action_at' => now(),
        ]);
        
        $this->executeNextStep($ticketWorkflow);
        Log::info("Resumed workflow for ticket {$ticketWorkflow->ticket_id}");
    }

    /**
     * ยกเลิก workflow
     */
    public function cancelWorkflow(TicketWorkflow $ticketWorkflow)
    {
        $ticketWorkflow->update([
            'status' => 'cancelled',
            'next_action_at' => null,
        ]);
        
        Log::info("Cancelled workflow for ticket {$ticketWorkflow->ticket_id}");
    }
}
