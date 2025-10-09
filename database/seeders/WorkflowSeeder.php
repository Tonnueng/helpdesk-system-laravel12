<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // หาข้อมูลพื้นฐาน
        $newStatus = Status::where('name', 'New')->first();
        $inProgressStatus = Status::where('name', 'In Progress')->first();
        $resolvedStatus = Status::where('name', 'Resolved')->first();
        
        $criticalPriority = Priority::where('name', 'Critical')->first();
        $highPriority = Priority::where('name', 'High')->first();
        
        // หาผู้ใช้ที่มีสิทธิ์เป็น leader, manager, ceo
        $leaders = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();
        $firstLeader = $leaders->first();
        
        // Workflow 1: Critical Priority Auto-Assignment
        if ($criticalPriority && $firstLeader) {
            $workflow1 = Workflow::create([
                'name' => 'Critical Priority Auto-Assignment',
                'description' => 'มอบหมายปัญหา Critical ให้หัวหน้าทีมโดยอัตโนมัติ',
                'trigger_type' => 'priority_based',
                'trigger_conditions' => ['priority_id' => $criticalPriority->id],
                'is_active' => true,
                'sort_order' => 1,
            ]);

            // Step 1: มอบหมายให้หัวหน้าทีม
            WorkflowStep::create([
                'workflow_id' => $workflow1->id,
                'name' => 'มอบหมายให้หัวหน้าทีม',
                'description' => 'มอบหมายปัญหา Critical ให้หัวหน้าทีม',
                'step_order' => 1,
                'action_type' => 'assign',
                'action_config' => ['user_id' => $firstLeader->id],
                'conditions' => [],
                'is_required' => true,
            ]);

            // Step 2: เปลี่ยนสถานะเป็น In Progress
            if ($inProgressStatus) {
                WorkflowStep::create([
                    'workflow_id' => $workflow1->id,
                    'name' => 'เปลี่ยนสถานะเป็น In Progress',
                    'description' => 'เปลี่ยนสถานะปัญหาเป็น In Progress',
                    'step_order' => 2,
                    'action_type' => 'status_change',
                    'action_config' => ['status_id' => $inProgressStatus->id],
                    'conditions' => [],
                    'is_required' => true,
                ]);
            }

            // Step 3: ส่งการแจ้งเตือน
            WorkflowStep::create([
                'workflow_id' => $workflow1->id,
                'name' => 'ส่งการแจ้งเตือน',
                'description' => 'ส่งการแจ้งเตือนให้ผู้เกี่ยวข้อง',
                'step_order' => 3,
                'action_type' => 'notification',
                'action_config' => ['message' => 'ปัญหา Critical ได้รับการมอบหมายและเปลี่ยนสถานะเป็น In Progress แล้ว'],
                'conditions' => [],
                'is_required' => true,
            ]);
        }

        // Workflow 2: High Priority Escalation
        if ($highPriority) {
            $workflow2 = Workflow::create([
                'name' => 'High Priority Escalation',
                'description' => 'ส่งต่อปัญหา High Priority ให้ผู้จัดการ',
                'trigger_type' => 'priority_based',
                'trigger_conditions' => ['priority_id' => $highPriority->id],
                'is_active' => true,
                'sort_order' => 2,
            ]);

            // Step 1: ส่งต่อให้ผู้จัดการ
            WorkflowStep::create([
                'workflow_id' => $workflow2->id,
                'name' => 'ส่งต่อให้ผู้จัดการ',
                'description' => 'ส่งต่อปัญหา High Priority ให้ผู้จัดการ',
                'step_order' => 1,
                'action_type' => 'escalation',
                'action_config' => [],
                'conditions' => [],
                'is_required' => true,
            ]);

            // Step 2: ส่งอีเมลแจ้งเตือน
            WorkflowStep::create([
                'workflow_id' => $workflow2->id,
                'name' => 'ส่งอีเมลแจ้งเตือน',
                'description' => 'ส่งอีเมลแจ้งเตือนให้ผู้จัดการ',
                'step_order' => 2,
                'action_type' => 'email_notification',
                'action_config' => [
                    'subject' => 'High Priority Issue Escalation',
                    'message' => 'มีปัญหา High Priority ที่ต้องการการดูแลเป็นพิเศษ'
                ],
                'conditions' => [],
                'is_required' => true,
            ]);
        }

        // Workflow 3: Auto Reply for New Tickets
        $workflow3 = Workflow::create([
            'name' => 'Auto Reply for New Tickets',
            'description' => 'ตอบกลับอัตโนมัติสำหรับปัญหาใหม่',
            'trigger_type' => 'status_based',
            'trigger_conditions' => ['status_id' => $newStatus ? $newStatus->id : null],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Step 1: ตอบกลับอัตโนมัติ
        WorkflowStep::create([
            'workflow_id' => $workflow3->id,
            'name' => 'ตอบกลับอัตโนมัติ',
            'description' => 'ส่งข้อความตอบกลับอัตโนมัติให้ผู้แจ้งปัญหา',
            'step_order' => 1,
            'action_type' => 'auto_reply',
            'action_config' => [
                'message' => 'ขอบคุณสำหรับการแจ้งปัญหา เราจะดำเนินการแก้ไขโดยเร็วที่สุด หากมีข้อสงสัยเพิ่มเติม สามารถติดต่อเราได้ตลอดเวลา'
            ],
            'conditions' => [],
            'is_required' => true,
        ]);

        // Step 2: เพิ่มความคิดเห็นในระบบ
        WorkflowStep::create([
            'workflow_id' => $workflow3->id,
            'name' => 'เพิ่มความคิดเห็นในระบบ',
            'description' => 'เพิ่มความคิดเห็นในระบบเพื่อติดตาม',
            'step_order' => 2,
            'action_type' => 'add_comment',
            'action_config' => [
                'comment' => 'ระบบได้ส่งข้อความตอบกลับอัตโนมัติให้ผู้แจ้งปัญหาแล้ว'
            ],
            'conditions' => [],
            'is_required' => true,
        ]);

        // Workflow 4: Resolved Ticket Follow-up
        if ($resolvedStatus) {
            $workflow4 = Workflow::create([
                'name' => 'Resolved Ticket Follow-up',
                'description' => 'ติดตามปัญหา Resolved หลังจาก 24 ชั่วโมง',
                'trigger_type' => 'status_based',
                'trigger_conditions' => ['status_id' => $resolvedStatus->id],
                'is_active' => true,
                'sort_order' => 4,
            ]);

            // Step 1: รอ 24 ชั่วโมง
            WorkflowStep::create([
                'workflow_id' => $workflow4->id,
                'name' => 'รอ 24 ชั่วโมง',
                'description' => 'รอ 24 ชั่วโมงก่อนติดตาม',
                'step_order' => 1,
                'action_type' => 'wait_for_response',
                'action_config' => ['hours' => 24],
                'conditions' => ['time_delay' => 1440], // 24 ชั่วโมง = 1440 นาที
                'is_required' => true,
            ]);

            // Step 2: ส่งการแจ้งเตือนติดตาม
            WorkflowStep::create([
                'workflow_id' => $workflow4->id,
                'name' => 'ส่งการแจ้งเตือนติดตาม',
                'description' => 'ส่งการแจ้งเตือนติดตามปัญหา',
                'step_order' => 2,
                'action_type' => 'notification',
                'action_config' => [
                    'message' => 'ปัญหาที่แก้ไขแล้วได้รับการติดตาม หากยังมีปัญหาเพิ่มเติม กรุณาแจ้งให้เราทราบ'
                ],
                'conditions' => [],
                'is_required' => true,
            ]);
        }

        // Workflow 5: Category-based Assignment (IT Issues)
        $itCategory = Category::where('name', 'IT Support')->first();
        if ($itCategory && $leaders->count() > 0) {
            $workflow5 = Workflow::create([
                'name' => 'IT Support Auto-Assignment',
                'description' => 'มอบหมายปัญหาด้าน IT Support ให้หัวหน้าทีม IT',
                'trigger_type' => 'category_based',
                'trigger_conditions' => ['category_id' => $itCategory->id],
                'is_active' => true,
                'sort_order' => 5,
            ]);

            // Step 1: มอบหมายให้หัวหน้าทีม IT
            $itLeader = $leaders->where('department', 'IT')->first() ?? $leaders->first();
            WorkflowStep::create([
                'workflow_id' => $workflow5->id,
                'name' => 'มอบหมายให้หัวหน้าทีม IT',
                'description' => 'มอบหมายปัญหาด้าน IT ให้หัวหน้าทีม',
                'step_order' => 1,
                'action_type' => 'assign',
                'action_config' => ['user_id' => $itLeader->id],
                'conditions' => [],
                'is_required' => true,
            ]);

            // Step 2: กำหนดวันครบกำหนด
            WorkflowStep::create([
                'workflow_id' => $workflow5->id,
                'name' => 'กำหนดวันครบกำหนด',
                'description' => 'กำหนดวันครบกำหนดการแก้ไขปัญหา IT',
                'step_order' => 2,
                'action_type' => 'set_due_date',
                'action_config' => ['hours' => 48], // 48 ชั่วโมง
                'conditions' => [],
                'is_required' => true,
            ]);

            // Step 3: ส่งการแจ้งเตือน
            WorkflowStep::create([
                'workflow_id' => $workflow5->id,
                'name' => 'ส่งการแจ้งเตือน',
                'description' => 'ส่งการแจ้งเตือนให้ทีม IT',
                'step_order' => 3,
                'action_type' => 'notification',
                'action_config' => [
                    'message' => 'ปัญหา IT Support ใหม่ได้รับการมอบหมายและกำหนดวันครบกำหนดแล้ว'
                ],
                'conditions' => [],
                'is_required' => true,
            ]);
        }

        $this->command->info('Workflow seeder completed successfully!');
        $this->command->info('Created ' . Workflow::count() . ' workflows with ' . WorkflowStep::count() . ' total steps.');
    }
}