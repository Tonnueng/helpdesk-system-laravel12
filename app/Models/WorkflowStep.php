<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'name',
        'description',
        'step_order',
        'action_type',
        'action_config',
        'conditions',
        'is_required',
    ];

    protected $casts = [
        'action_config' => 'array',
        'conditions' => 'array',
        'is_required' => 'boolean',
        'step_order' => 'integer',
    ];

    // Relationships
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function ticketWorkflows(): HasMany
    {
        return $this->hasMany(TicketWorkflow::class, 'current_step_id');
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Methods
    public function getActionTypeTextAttribute()
    {
        $types = [
            'assign' => 'มอบหมายงาน',
            'status_change' => 'เปลี่ยนสถานะ',
            'notification' => 'ส่งการแจ้งเตือน',
            'auto_reply' => 'ตอบกลับอัตโนมัติ',
            'escalation' => 'ส่งต่อให้ผู้จัดการ',
            'email_notification' => 'ส่งอีเมลแจ้งเตือน',
            'slack_notification' => 'ส่งแจ้งเตือน Slack',
            'wait_for_response' => 'รอการตอบกลับ',
            'set_due_date' => 'กำหนดวันครบกำหนด',
            'add_comment' => 'เพิ่มความคิดเห็น',
        ];

        return $types[$this->action_type] ?? $this->action_type;
    }

    public function getActionDescriptionAttribute()
    {
        $config = $this->action_config ?? [];
        
        switch ($this->action_type) {
            case 'assign':
                if (isset($config['user_id'])) {
                    $user = User::find($config['user_id']);
                    return "มอบหมายให้: " . ($user ? $user->name : 'ไม่ระบุ');
                }
                return 'มอบหมายให้ผู้ใช้ที่กำหนด';
                
            case 'status_change':
                if (isset($config['status_id'])) {
                    $status = Status::find($config['status_id']);
                    return "เปลี่ยนสถานะเป็น: " . ($status ? $status->name : 'ไม่ระบุ');
                }
                return 'เปลี่ยนสถานะตามที่กำหนด';
                
            case 'notification':
                return "ส่งการแจ้งเตือน: " . ($config['message'] ?? 'ข้อความแจ้งเตือน');
                
            case 'auto_reply':
                return "ตอบกลับอัตโนมัติ: " . ($config['message'] ?? 'ข้อความตอบกลับ');
                
            case 'escalation':
                return "ส่งต่อให้ผู้จัดการ";
                
            case 'email_notification':
                return "ส่งอีเมลแจ้งเตือน: " . ($config['subject'] ?? 'หัวข้ออีเมล');
                
            case 'wait_for_response':
                $hours = $config['hours'] ?? 24;
                return "รอการตอบกลับเป็นเวลา {$hours} ชั่วโมง";
                
            case 'set_due_date':
                $hours = $config['hours'] ?? 48;
                return "กำหนดวันครบกำหนดใน {$hours} ชั่วโมง";
                
            case 'add_comment':
                return "เพิ่มความคิดเห็น: " . ($config['comment'] ?? 'ความคิดเห็น');
                
            default:
                return 'ไม่ระบุการกระทำ';
        }
    }

    public function shouldExecute($ticketWorkflow)
    {
        $conditions = $this->conditions ?? [];
        
        // ตรวจสอบเงื่อนไขการทำงาน
        if (isset($conditions['time_delay'])) {
            $delay = $conditions['time_delay']; // ในนาที
            $nextActionAt = $ticketWorkflow->next_action_at;
            
            if (!$nextActionAt || now()->lt($nextActionAt)) {
                return false;
            }
        }

        if (isset($conditions['user_response_required'])) {
            // ตรวจสอบว่าผู้ใช้ตอบกลับหรือไม่
            $lastUpdate = $ticketWorkflow->ticket->updates()
                ->where('created_at', '>', $ticketWorkflow->updated_at)
                ->first();
            
            if (!$lastUpdate) {
                return false;
            }
        }

        return true;
    }
}