# 🔄 ระบบ Workflow - Helpdesk System

## 📋 ภาพรวม

ระบบ Workflow เป็นระบบที่ช่วยกำหนดขั้นตอนการทำงานอัตโนมัติสำหรับการจัดการปัญหา (Tickets) ในระบบ Helpdesk โดยสามารถตั้งค่าให้ระบบทำงานตามขั้นตอนที่กำหนดไว้ล่วงหน้าได้อย่างอัตโนมัติ

## 🏗️ โครงสร้างระบบ

### 1. ตารางหลัก

#### `workflows` - ข้อมูล Workflow
- `id` - รหัส Workflow
- `name` - ชื่อ Workflow
- `description` - คำอธิบาย
- `trigger_type` - ประเภทการเรียกใช้ (auto, manual, category_based, priority_based, status_based)
- `trigger_conditions` - เงื่อนไขการเรียกใช้ (JSON)
- `is_active` - สถานะเปิด/ปิด
- `sort_order` - ลำดับการเรียง

#### `workflow_steps` - ขั้นตอนใน Workflow
- `id` - รหัสขั้นตอน
- `workflow_id` - รหัส Workflow
- `name` - ชื่อขั้นตอน
- `description` - คำอธิบายขั้นตอน
- `step_order` - ลำดับขั้นตอน
- `action_type` - ประเภทการกระทำ
- `action_config` - การตั้งค่าการกระทำ (JSON)
- `conditions` - เงื่อนไขการทำงาน (JSON)
- `is_required` - ขั้นตอนบังคับหรือไม่

#### `ticket_workflows` - การติดตาม Workflow ของ Ticket
- `id` - รหัส
- `ticket_id` - รหัส Ticket
- `workflow_id` - รหัส Workflow
- `current_step_id` - รหัสขั้นตอนปัจจุบัน
- `status` - สถานะ (running, completed, paused, cancelled)
- `completed_steps` - ขั้นตอนที่เสร็จแล้ว (JSON)
- `step_data` - ข้อมูลที่เก็บจากแต่ละขั้นตอน (JSON)
- `started_at` - เวลาเริ่มต้น
- `completed_at` - เวลาเสร็จสิ้น
- `next_action_at` - เวลาที่จะดำเนินการขั้นตอนถัดไป

## 🎯 ประเภทการเรียกใช้ (Trigger Types)

### 1. `auto` - อัตโนมัติ
- เรียกใช้ทุกครั้งที่สร้าง Ticket ใหม่

### 2. `manual` - ด้วยตนเอง
- เรียกใช้ด้วยตนเองผ่านหน้าเว็บ

### 3. `category_based` - ตามประเภทปัญหา
- เรียกใช้เมื่อประเภทปัญหาเป็นตามที่กำหนด

### 4. `priority_based` - ตามระดับความสำคัญ
- เรียกใช้เมื่อระดับความสำคัญเป็นตามที่กำหนด

### 5. `status_based` - ตามสถานะ
- เรียกใช้เมื่อสถานะเป็นตามที่กำหนด

## ⚙️ ประเภทการกระทำ (Action Types)

### 1. `assign` - มอบหมายงาน
- มอบหมาย Ticket ให้ผู้ใช้ที่กำหนด

### 2. `status_change` - เปลี่ยนสถานะ
- เปลี่ยนสถานะของ Ticket

### 3. `notification` - ส่งการแจ้งเตือน
- ส่งการแจ้งเตือนในระบบ

### 4. `auto_reply` - ตอบกลับอัตโนมัติ
- ส่งข้อความตอบกลับอัตโนมัติ

### 5. `escalation` - ส่งต่อให้ผู้จัดการ
- ส่งต่อ Ticket ให้ผู้จัดการ

### 6. `email_notification` - ส่งอีเมลแจ้งเตือน
- ส่งอีเมลแจ้งเตือน

### 7. `wait_for_response` - รอการตอบกลับ
- รอการตอบกลับจากผู้ใช้

### 8. `set_due_date` - กำหนดวันครบกำหนด
- กำหนดวันครบกำหนดการแก้ไข

### 9. `add_comment` - เพิ่มความคิดเห็น
- เพิ่มความคิดเห็นใน Ticket

## 🚀 การใช้งาน

### 1. การสร้าง Workflow

```php
// สร้าง Workflow ใหม่
$workflow = Workflow::create([
    'name' => 'Critical Priority Auto-Assignment',
    'description' => 'มอบหมายปัญหา Critical ให้หัวหน้าทีมโดยอัตโนมัติ',
    'trigger_type' => 'priority_based',
    'trigger_conditions' => ['priority_id' => $criticalPriority->id],
    'is_active' => true,
    'sort_order' => 1,
]);

// เพิ่มขั้นตอน
WorkflowStep::create([
    'workflow_id' => $workflow->id,
    'name' => 'มอบหมายให้หัวหน้าทีม',
    'step_order' => 1,
    'action_type' => 'assign',
    'action_config' => ['user_id' => $leader->id],
]);
```

### 2. การเริ่มต้น Workflow

```php
// เริ่มต้น Workflow สำหรับ Ticket
$workflowService = new WorkflowService();
$result = $workflowService->startWorkflow($ticket, $workflow);
```

### 3. การประมวลผล Workflow

```php
// ประมวลผล Workflow ที่พร้อมดำเนินการ
$workflowService->processDueWorkflows();
```

## 📱 หน้าเว็บ

### 1. รายการ Workflow (`/workflows`)
- แสดงรายการ Workflow ทั้งหมด
- ค้นหาและกรอง Workflow
- จัดการสถานะ Workflow

### 2. สร้าง Workflow (`/workflows/create`)
- สร้าง Workflow ใหม่
- กำหนดเงื่อนไขการเรียกใช้
- เพิ่มขั้นตอนการทำงาน

### 3. แก้ไข Workflow (`/workflows/{id}/edit`)
- แก้ไขข้อมูล Workflow
- เพิ่ม/แก้ไข/ลบขั้นตอน

### 4. ดูรายละเอียด Workflow (`/workflows/{id}`)
- ดูข้อมูล Workflow และขั้นตอน
- ดูประวัติการใช้งาน

## 🔧 การตั้งค่า Cron Job

เพื่อให้ระบบ Workflow ทำงานอัตโนมัติ ต้องตั้งค่า Cron Job:

```bash
# เพิ่มใน crontab
* * * * * cd /path/to/project && php artisan workflows:process >> /dev/null 2>&1
```

หรือใช้ Laravel Scheduler:

```php
// ใน app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('workflows:process')->everyMinute();
}
```

## 📊 ตัวอย่าง Workflow

### 1. Critical Priority Auto-Assignment
```
เมื่อ: ระดับความสำคัญ = Critical
ขั้นตอน:
1. มอบหมายให้หัวหน้าทีม
2. เปลี่ยนสถานะเป็น In Progress
3. ส่งการแจ้งเตือน
```

### 2. High Priority Escalation
```
เมื่อ: ระดับความสำคัญ = High
ขั้นตอน:
1. ส่งต่อให้ผู้จัดการ
2. ส่งอีเมลแจ้งเตือน
```

### 3. Auto Reply for New Tickets
```
เมื่อ: สถานะ = New
ขั้นตอน:
1. ตอบกลับอัตโนมัติ
2. เพิ่มความคิดเห็นในระบบ
```

### 4. Resolved Ticket Follow-up
```
เมื่อ: สถานะ = Resolved
ขั้นตอน:
1. รอ 24 ชั่วโมง
2. ส่งการแจ้งเตือนติดตาม
```

## 🛠️ การพัฒนาเพิ่มเติม

### 1. เพิ่ม Action Type ใหม่

```php
// ใน WorkflowService
private function executeNewActionType(Ticket $ticket, array $config)
{
    // โค้ดการทำงาน
    return true;
}
```

### 2. เพิ่ม Condition ใหม่

```php
// ใน WorkflowStep model
public function shouldExecute($ticketWorkflow)
{
    // เพิ่มเงื่อนไขใหม่
    if (isset($this->conditions['new_condition'])) {
        // ตรวจสอบเงื่อนไข
    }
    
    return true;
}
```

### 3. เพิ่ม Trigger Type ใหม่

```php
// ใน Workflow model
public function canTrigger($ticket)
{
    if ($this->trigger_type === 'new_trigger_type') {
        // ตรวจสอบเงื่อนไขใหม่
    }
    
    return true;
}
```

## 📈 การติดตามและวิเคราะห์

### 1. สถิติ Workflow
- จำนวน Workflow ที่ใช้งาน
- จำนวน Ticket ที่ผ่าน Workflow
- เวลาเฉลี่ยในการดำเนินการ

### 2. การติดตามประสิทธิภาพ
- ขั้นตอนที่ใช้เวลานานที่สุด
- Workflow ที่ใช้บ่อยที่สุด
- อัตราการสำเร็จของ Workflow

## 🔒 ความปลอดภัย

### 1. สิทธิ์การเข้าถึง
- เฉพาะ Manager และ CEO เท่านั้นที่สามารถจัดการ Workflow ได้
- ผู้ใช้ทั่วไปสามารถดูสถานะ Workflow ของ Ticket ของตนเองได้

### 2. การตรวจสอบข้อมูล
- ตรวจสอบข้อมูลที่ส่งเข้ามา
- ป้องกัน SQL Injection
- ตรวจสอบสิทธิ์การเข้าถึง

## 🚨 การแก้ไขปัญหา

### 1. Workflow ไม่ทำงาน
- ตรวจสอบสถานะ `is_active`
- ตรวจสอบเงื่อนไข `trigger_conditions`
- ตรวจสอบ Cron Job

### 2. ขั้นตอนไม่ดำเนินการ
- ตรวจสอบ `next_action_at`
- ตรวจสอบเงื่อนไข `conditions`
- ตรวจสอบ Log ไฟล์

### 3. การแจ้งเตือนไม่ส่ง
- ตรวจสอบการตั้งค่า Mail
- ตรวจสอบ Notification Service
- ตรวจสอบ Queue System

## 📝 หมายเหตุ

- ระบบ Workflow ทำงานแบบ Asynchronous
- ใช้ Queue System สำหรับการประมวลผล
- รองรับการทำงานแบบ Real-time
- สามารถขยายได้ง่ายสำหรับความต้องการในอนาคต
