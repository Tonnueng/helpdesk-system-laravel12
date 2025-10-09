<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // หาข้อมูลที่จำเป็น
        $users = User::all();
        $priorities = Priority::all();
        $statuses = Status::all();

        if ($users->isEmpty() || $priorities->isEmpty() || $statuses->isEmpty()) {
            $this->command->error('Missing required data. Please run other seeders first.');
            return;
        }

        // สร้างข้อมูลตัวอย่าง Ticket 30 ปัญหา สำหรับแต่ละแผนก
        $sampleTickets = [
            // Operation & Production (4 ปัญหา)
            [
                'title' => 'เครื่องจักรหยุดทำงานกะทันหัน',
                'description' => 'เครื่องจักรสายการผลิตหลักหยุดทำงานกะทันหันในขณะกำลังผลิตสินค้า ทำให้ต้องหยุดการผลิตทั้งหมด',
                'main_category' => 'Operation & Production',
                'sub_category' => 'การผลิต',
                'priority' => 'High',
                'reported_at' => now()->subDays(3),
            ],
            [
                'title' => 'วัสดุบรรจุภัณฑ์ไม่เพียงพอ',
                'description' => 'วัสดุบรรจุภัณฑ์สำหรับสินค้าชนิดใหม่ไม่เพียงพอ อาจส่งผลต่อการส่งมอบสินค้าให้ลูกค้า',
                'main_category' => 'Operation & Production',
                'sub_category' => 'การแพ็ค',
                'priority' => 'Medium',
                'reported_at' => now()->subDays(2),
            ],
            [
                'title' => 'รถขนส่งเสียระหว่างทาง',
                'description' => 'รถขนส่งสินค้าเสียระหว่างทางไปส่งลูกค้า ทำให้สินค้าส่งล่าช้าและอาจเสียหายจากความร้อน',
                'main_category' => 'Operation & Production',
                'sub_category' => 'การจัดส่ง',
                'priority' => 'High',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'ระบบจัดการคลังสินค้าขัดข้อง',
                'description' => 'ระบบจัดการคลังสินค้าไม่สามารถอัปเดตสต็อกได้ ทำให้ไม่ทราบจำนวนสินค้าคงเหลือที่แท้จริง',
                'main_category' => 'Operation & Production',
                'sub_category' => 'คลังสินค้า',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(6),
            ],

            // การขายและลูกค้า (4 ปัญหา)
            [
                'title' => 'ระบบรับคำสั่งซื้อออนไลน์ล่ม',
                'description' => 'ระบบรับคำสั่งซื้อออนไลน์ล่มเป็นเวลา 2 ชั่วโมง ทำให้สูญเสียลูกค้าและรายได้',
                'main_category' => 'Sales & Customer',
                'sub_category' => 'คำสั่งซื้อ',
                'priority' => 'Critical',
                'reported_at' => now()->subHours(4),
            ],
            [
                'title' => 'ลูกค้าร้องเรียนการคืนเงินล่าช้า',
                'description' => 'ลูกค้าร้องเรียนว่าการคืนเงินล่าช้ากว่ากำหนด 7 วัน ทำให้ความเชื่อมั่นในแบรนด์ลดลง',
                'main_category' => 'Sales & Customer',
                'sub_category' => 'การคืนเงิน',
                'priority' => 'High',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'ลูกค้าร้องเรียนสินค้าชำรุด',
                'description' => 'ลูกค้ารายใหญ่ร้องเรียนว่าสินค้าที่ได้รับมีชำรุดและไม่ตรงตามสเปคที่สั่งซื้อ',
                'main_category' => 'Sales & Customer',
                'sub_category' => 'การร้องเรียนจากลูกค้า',
                'priority' => 'High',
                'reported_at' => now()->subHours(2),
            ],
            [
                'title' => 'ระบบ CRM ไม่สามารถบันทึกข้อมูลลูกค้าใหม่',
                'description' => 'ระบบ CRM ไม่สามารถบันทึกข้อมูลลูกค้าใหม่ได้ ทำให้ทีมขายไม่สามารถติดตามลูกค้าได้',
                'main_category' => 'Sales & Customer',
                'sub_category' => 'คำสั่งซื้อ',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(8),
            ],

            // การตลาดและโฆษณา (4 ปัญหา)
            [
                'title' => 'ประสิทธิภาพโฆษณา Facebook ลดลง',
                'description' => 'ประสิทธิภาพโฆษณา Facebook ลดลงอย่างมาก ทำให้ค่าใช้จ่ายต่อลูกค้าเพิ่มขึ้น 40%',
                'main_category' => 'Marketing & Ads',
                'sub_category' => 'ประสิทธิภาพโฆษณา',
                'priority' => 'High',
                'reported_at' => now()->subDays(2),
            ],
            [
                'title' => 'แคมเปญโปรโมชั่นไม่สามารถเปิดใช้งานได้',
                'description' => 'แคมเปญโปรโมชั่นสำหรับช่วงเทศกาลไม่สามารถเปิดใช้งานได้ ทำให้สูญเสียโอกาสทางการตลาด',
                'main_category' => 'Marketing & Ads',
                'sub_category' => 'ปัญหาแคมเปญ',
                'priority' => 'Critical',
                'reported_at' => now()->subHours(3),
            ],
            [
                'title' => 'คอนเทนต์สำหรับโซเชียลมีเดียล่าช้า',
                'description' => 'คอนเทนต์สำหรับโซเชียลมีเดียล่าช้ากว่ากำหนด 2 วัน ทำให้แผนการตลาดต้องเลื่อนออกไป',
                'main_category' => 'Marketing & Ads',
                'sub_category' => 'ความล่าช้าด้านคอนเทนต์',
                'priority' => 'Medium',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'ระบบวิเคราะห์ข้อมูลการตลาดขัดข้อง',
                'description' => 'ระบบวิเคราะห์ข้อมูลการตลาดขัดข้อง ทำให้ไม่สามารถประเมินประสิทธิภาพแคมเปญได้',
                'main_category' => 'Marketing & Ads',
                'sub_category' => 'ประสิทธิภาพโฆษณา',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(5),
            ],

            // การเงินและบัญชี (4 ปัญหา)
            [
                'title' => 'กระแสเงินสดติดขัด',
                'description' => 'กระแสเงินสดติดขัดเนื่องจากลูกค้าชำระเงินล่าช้า ทำให้ไม่สามารถจ่ายเงินซัพพลายเออร์ได้ทันเวลา',
                'main_category' => 'Finance & Accounting',
                'sub_category' => 'กระแสเงินสด',
                'priority' => 'Critical',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'ระบบชำระเงินออนไลน์ขัดข้อง',
                'description' => 'ระบบชำระเงินออนไลน์ขัดข้อง ทำให้ลูกค้าไม่สามารถชำระเงินได้ และสูญเสียรายได้',
                'main_category' => 'Finance & Accounting',
                'sub_category' => 'ปัญหาการชำระเงิน',
                'priority' => 'Critical',
                'reported_at' => now()->subHours(2),
            ],
            [
                'title' => 'ต้นทุนการผลิตเพิ่มขึ้นเกินงบประมาณ',
                'description' => 'ต้นทุนการผลิตเพิ่มขึ้นเกินงบประมาณที่วางไว้ 15% ทำให้ผลกำไรลดลง',
                'main_category' => 'Finance & Accounting',
                'sub_category' => 'การควบคุมต้นทุน',
                'priority' => 'High',
                'reported_at' => now()->subDays(3),
            ],
            [
                'title' => 'ระบบบัญชีไม่สามารถสร้างรายงานได้',
                'description' => 'ระบบบัญชีไม่สามารถสร้างรายงานงบการเงินประจำเดือนได้ ทำให้การรายงานผลล่าช้า',
                'main_category' => 'Finance & Accounting',
                'sub_category' => 'กระแสเงินสด',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(6),
            ],

            // ทรัพยากรบุคคล (4 ปัญหา)
            [
                'title' => 'พนักงานขาดงานเพิ่มขึ้นอย่างผิดปกติ',
                'description' => 'พนักงานในแผนกการผลิตขาดงานเพิ่มขึ้นอย่างผิดปกติ ทำให้การผลิตล่าช้า',
                'main_category' => 'People & HR',
                'sub_category' => 'การขาด/ลา/สาย',
                'priority' => 'High',
                'reported_at' => now()->subDays(2),
            ],
            [
                'title' => 'ประสิทธิภาพการทำงานของทีมลดลง',
                'description' => 'ประสิทธิภาพการทำงานของทีมขายลดลงอย่างเห็นได้ชัด ทำให้ยอดขายไม่เป็นไปตามเป้า',
                'main_category' => 'People & HR',
                'sub_category' => 'ประสิทธิภาพการทำงาน',
                'priority' => 'High',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'การสื่อสารระหว่างแผนกไม่ชัดเจน',
                'description' => 'การสื่อสารระหว่างแผนกไม่ชัดเจน ทำให้เกิดความเข้าใจผิดและงานซ้ำซ้อน',
                'main_category' => 'People & HR',
                'sub_category' => 'การสื่อสาร',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(4),
            ],
            [
                'title' => 'ระบบลงเวลาทำงานขัดข้อง',
                'description' => 'ระบบลงเวลาทำงานขัดข้อง ทำให้ไม่สามารถคำนวณเงินเดือนได้ถูกต้อง',
                'main_category' => 'People & HR',
                'sub_category' => 'การขาด/ลา/สาย',
                'priority' => 'High',
                'reported_at' => now()->subHours(8),
            ],

            // IT & Systems (4 ปัญหา)
            [
                'title' => 'เซิร์ฟเวอร์หลักล่ม',
                'description' => 'เซิร์ฟเวอร์หลักล่มเป็นเวลา 3 ชั่วโมง ทำให้ระบบทั้งหมดไม่สามารถใช้งานได้',
                'main_category' => 'IT & Systems',
                'sub_category' => 'ปัญหาระบบ',
                'priority' => 'Critical',
                'reported_at' => now()->subHours(3),
            ],
            [
                'title' => 'คอมพิวเตอร์ในแผนกขายเสียหลายเครื่อง',
                'description' => 'คอมพิวเตอร์ในแผนกขายเสียหลายเครื่องพร้อมกัน ทำให้ไม่สามารถทำงานได้',
                'main_category' => 'IT & Systems',
                'sub_category' => 'ฮาร์ดแวร์',
                'priority' => 'High',
                'reported_at' => now()->subHours(1),
            ],
            [
                'title' => 'ระบบรักษาความปลอดภัยถูกโจมตี',
                'description' => 'ระบบรักษาความปลอดภัยถูกโจมตีจากไวรัส ทำให้ข้อมูลอาจถูกขโมย',
                'main_category' => 'IT & Systems',
                'sub_category' => 'ความปลอดภัย',
                'priority' => 'Critical',
                'reported_at' => now()->subHours(2),
            ],
            [
                'title' => 'ระบบสำรองข้อมูลไม่ทำงาน',
                'description' => 'ระบบสำรองข้อมูลไม่ทำงานเป็นเวลา 1 สัปดาห์ ทำให้ข้อมูลอาจสูญหายได้',
                'main_category' => 'IT & Systems',
                'sub_category' => 'ปัญหาระบบ',
                'priority' => 'High',
                'reported_at' => now()->subDays(1),
            ],

            // Supplier & Partner (3 ปัญหา)
            [
                'title' => 'ซัพพลายเออร์ส่งสินค้าล่าช้า',
                'description' => 'ซัพพลายเออร์หลักส่งสินค้าล่าช้ากว่ากำหนด 5 วัน ทำให้การผลิตต้องหยุดชะงัก',
                'main_category' => 'Supplier & Partner',
                'sub_category' => 'ความล่าช้า',
                'priority' => 'High',
                'reported_at' => now()->subDays(2),
            ],
            [
                'title' => 'คุณภาพสินค้าจากซัพพลายเออร์ไม่ตรงตามสเปค',
                'description' => 'คุณภาพสินค้าจากซัพพลายเออร์ไม่ตรงตามสเปคที่ตกลงกัน ทำให้ต้องส่งคืนและหาซัพพลายเออร์ใหม่',
                'main_category' => 'Supplier & Partner',
                'sub_category' => 'คุณภาพสินค้า',
                'priority' => 'High',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'ราคาวัตถุดิบเพิ่มขึ้นอย่างกะทันหัน',
                'description' => 'ราคาวัตถุดิบเพิ่มขึ้นอย่างกะทันหัน 30% ทำให้ต้นทุนการผลิตเพิ่มขึ้นมาก',
                'main_category' => 'Supplier & Partner',
                'sub_category' => 'ราคาผันผวน',
                'priority' => 'Medium',
                'reported_at' => now()->subHours(4),
            ],

            // Strategy & Management (3 ปัญหา)
            [
                'title' => 'นโยบายใหม่ไม่ชัดเจน',
                'description' => 'นโยบายใหม่ที่ออกมาไม่ชัดเจน ทำให้พนักงานไม่เข้าใจและปฏิบัติตามไม่ถูกต้อง',
                'main_category' => 'Strategy & Management',
                'sub_category' => 'นโยบาย',
                'priority' => 'Medium',
                'reported_at' => now()->subDays(1),
            ],
            [
                'title' => 'การจัดสรร KPI ไม่เป็นธรรม',
                'description' => 'การจัดสรร KPI ไม่เป็นธรรม ทำให้พนักงานบางคนไม่พอใจและประสิทธิภาพลดลง',
                'main_category' => 'Strategy & Management',
                'sub_category' => 'การจัดสรร KPI',
                'priority' => 'Medium',
                'reported_at' => now()->subDays(2),
            ],
            [
                'title' => 'การตัดสินใจล่าช้าเกินไป',
                'description' => 'การตัดสินใจในเรื่องสำคัญล่าช้าเกินไป ทำให้เสียโอกาสทางธุรกิจ',
                'main_category' => 'Strategy & Management',
                'sub_category' => 'ความล่าช้าในการตัดสินใจ',
                'priority' => 'High',
                'reported_at' => now()->subHours(6),
            ],
        ];

        $createdCount = 0;
        foreach ($sampleTickets as $ticketData) {
            // หาผู้ใช้แบบสุ่ม
            $user = $users->random();
            
            // หาหมวดหมู่ย่อย
            $subCategory = Category::where('name', $ticketData['sub_category'])
                                 ->where('parent_category', $ticketData['main_category'])
                                 ->first();
            
            if (!$subCategory) {
                $this->command->warn("Sub category not found: {$ticketData['sub_category']} for {$ticketData['main_category']}");
                continue;
            }
            
            // หา Priority
            $priority = $priorities->where('name', $ticketData['priority'])->first();
            if (!$priority) {
                $priority = $priorities->random();
            }
            
            // หา Status แบบสุ่ม
            $status = $statuses->random();
            
            // หาหัวหน้าทีมตามหมวดหมู่หลัก
            $assignedLeader = $this->findLeaderByMainCategory($ticketData['main_category']);

            Ticket::create([
                'user_id' => $user->id,
                'category_id' => $subCategory->id,
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'title' => $ticketData['title'],
                'description' => $ticketData['description'],
                'reported_at' => $ticketData['reported_at'],
                'assigned_to_user_id' => $assignedLeader ? $assignedLeader->id : null,
            ]);
            
            $createdCount++;
        }

        $this->command->info("Created {$createdCount} sample tickets successfully!");
    }

    /**
     * หาหัวหน้าตามหมวดหมู่หลัก
     */
    private function findLeaderByMainCategory($mainCategory)
    {
        // Mapping หมวดหมู่หลักกับหัวหน้าทีม
        $categoryLeaderMapping = [
            'Operation & Production' => 'leader.operation@test.com',
            'Sales & Customer' => 'leader.sales@test.com',
            'Marketing & Ads' => 'leader.marketing@test.com',
            'Finance & Accounting' => 'leader.finance@test.com',
            'People & HR' => 'leader.hr@test.com',
            'IT & Systems' => 'leader.it@test.com',
            'Supplier & Partner' => 'leader.supplier@test.com',
            'Strategy & Management' => 'leader.strategy@test.com',
        ];

        $leaderEmail = $categoryLeaderMapping[$mainCategory] ?? null;
        
        if ($leaderEmail) {
            return User::where('email', $leaderEmail)->where('role', 'leader')->first();
        }

        return null;
    }
}
