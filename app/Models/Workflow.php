<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_conditions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function ticketWorkflows(): HasMany
    {
        return $this->hasMany(TicketWorkflow::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Methods
    public function canTrigger($ticket)
    {
        if (!$this->is_active) {
            return false;
        }

        // ตรวจสอบเงื่อนไขการเรียกใช้
        if ($this->trigger_type === 'category_based') {
            $conditions = $this->trigger_conditions ?? [];
            if (isset($conditions['category_id']) && $ticket->category_id != $conditions['category_id']) {
                return false;
            }
        }

        if ($this->trigger_type === 'priority_based') {
            $conditions = $this->trigger_conditions ?? [];
            if (isset($conditions['priority_id']) && $ticket->priority_id != $conditions['priority_id']) {
                return false;
            }
        }

        if ($this->trigger_type === 'status_based') {
            $conditions = $this->trigger_conditions ?? [];
            if (isset($conditions['status_id']) && $ticket->status_id != $conditions['status_id']) {
                return false;
            }
        }

        return true;
    }

    public function getTriggerConditionsTextAttribute()
    {
        $conditions = $this->trigger_conditions ?? [];
        
        switch ($this->trigger_type) {
            case 'auto':
                return 'เรียกใช้อัตโนมัติทุกครั้ง';
            case 'manual':
                return 'เรียกใช้ด้วยตนเอง';
            case 'category_based':
                if (isset($conditions['category_id'])) {
                    $category = Category::find($conditions['category_id']);
                    return "เมื่อประเภทปัญหาเป็น: " . ($category ? $category->name : 'ไม่ระบุ');
                }
                return 'เมื่อประเภทปัญหาเป็น: ไม่ระบุ';
            case 'priority_based':
                if (isset($conditions['priority_id'])) {
                    $priority = Priority::find($conditions['priority_id']);
                    return "เมื่อระดับความสำคัญเป็น: " . ($priority ? $priority->name : 'ไม่ระบุ');
                }
                return 'เมื่อระดับความสำคัญเป็น: ไม่ระบุ';
            case 'status_based':
                if (isset($conditions['status_id'])) {
                    $status = Status::find($conditions['status_id']);
                    return "เมื่อสถานะเป็น: " . ($status ? $status->name : 'ไม่ระบุ');
                }
                return 'เมื่อสถานะเป็น: ไม่ระบุ';
            default:
                return 'ไม่ระบุ';
        }
    }
}