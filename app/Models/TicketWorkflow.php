<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'workflow_id',
        'current_step_id',
        'status',
        'completed_steps',
        'step_data',
        'started_at',
        'completed_at',
        'next_action_at',
    ];

    protected $casts = [
        'completed_steps' => 'array',
        'step_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_action_at' => 'datetime',
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    // Scopes
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeDueForAction($query)
    {
        return $query->where('status', 'running')
                    ->where('next_action_at', '<=', now());
    }

    // Methods
    public function getStatusTextAttribute()
    {
        $statuses = [
            'running' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'paused' => 'หยุดชั่วคราว',
            'cancelled' => 'ยกเลิก',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function isRunning()
    {
        return $this->status === 'running';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isDueForAction()
    {
        return $this->isRunning() && 
               $this->next_action_at && 
               now()->gte($this->next_action_at);
    }

    public function markStepCompleted($stepId, $data = [])
    {
        $completedSteps = $this->completed_steps ?? [];
        $stepData = $this->step_data ?? [];
        
        if (!in_array($stepId, $completedSteps)) {
            $completedSteps[] = $stepId;
        }
        
        $stepData[$stepId] = array_merge($stepData[$stepId] ?? [], $data);
        
        $this->update([
            'completed_steps' => $completedSteps,
            'step_data' => $stepData,
        ]);
    }

    public function getNextStep()
    {
        if (!$this->workflow) {
            return null;
        }

        $completedSteps = $this->completed_steps ?? [];
        
        return $this->workflow->steps()
            ->whereNotIn('id', $completedSteps)
            ->orderBy('step_order')
            ->first();
    }

    public function moveToNextStep()
    {
        $nextStep = $this->getNextStep();
        
        if ($nextStep) {
            $this->update([
                'current_step_id' => $nextStep->id,
                'next_action_at' => $this->calculateNextActionTime($nextStep),
            ]);
        } else {
            // ไม่มีขั้นตอนถัดไป - workflow เสร็จสิ้น
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'current_step_id' => null,
                'next_action_at' => null,
            ]);
        }
    }

    private function calculateNextActionTime($step)
    {
        $conditions = $step->conditions ?? [];
        
        if (isset($conditions['time_delay'])) {
            $delayMinutes = $conditions['time_delay'];
            return now()->addMinutes($delayMinutes);
        }
        
        return now();
    }

    public function getProgressPercentage()
    {
        if (!$this->workflow) {
            return 0;
        }

        $totalSteps = $this->workflow->steps()->count();
        $completedSteps = count($this->completed_steps ?? []);
        
        if ($totalSteps === 0) {
            return 100;
        }
        
        return round(($completedSteps / $totalSteps) * 100);
    }
}