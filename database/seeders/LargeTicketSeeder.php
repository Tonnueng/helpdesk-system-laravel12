<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use Carbon\Carbon;

class LargeTicketSeeder extends Seeder
{
    public function run(): void
    {
        // ลบปัญหาเก่าทั้งหมด
        Ticket::query()->delete();

        // ดึงข้อมูลที่จำเป็น
        $users = User::all();
        $categories = Category::all();
        $priorities = Priority::all();
        $statuses = Status::all();

        if ($users->isEmpty() || $categories->isEmpty() || $priorities->isEmpty() || $statuses->isEmpty()) {
            $this->command->error('ไม่พบข้อมูลที่จำเป็นสำหรับสร้างปัญหา');
            return;
        }

        // สร้างปัญหา 120 ปัญหา
        $tickets = [];
        $ticketCount = 120;

        for ($i = 1; $i <= $ticketCount; $i++) {
            $tickets[] = [
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'priority_id' => $priorities->random()->id,
                'status_id' => $statuses->random()->id,
                'title' => $this->generateTicketTitle($i),
                'description' => $this->generateTicketDescription($i),
                'assigned_to_user_id' => $users->random()->id,
                'reported_at' => Carbon::now()->subDays(rand(0, 30)),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ];
        }

        // แบ่งเป็นชุดๆ เพื่อป้องกัน memory limit
        $chunks = array_chunk($tickets, 50);
        foreach ($chunks as $chunk) {
            Ticket::insert($chunk);
        }

        $this->command->info("สร้างปัญหา {$ticketCount} ปัญหาเรียบร้อยแล้ว");
    }

    private function generateTicketTitle(int $index): string
    {
        $titles = [
            'ระบบล็อกอินไม่ทำงาน',
            'หน้าจอแสดงผลผิดพลาด',
            'ข้อมูลไม่ถูกต้อง',
            'ระบบช้าเกินไป',
            'ไฟล์อัปโหลดไม่ได้',
            'การส่งอีเมลล้มเหลว',
            'ฐานข้อมูลเชื่อมต่อไม่ได้',
            'ระบบแจ้งเตือนไม่ทำงาน',
            'การพิมพ์เอกสารผิดพลาด',
            'ระบบสำรองข้อมูลล้มเหลว',
            'การเข้าถึงไฟล์ไม่ได้',
            'ระบบรายงานไม่แสดงผล',
            'การอัปเดตข้อมูลล้มเหลว',
            'ระบบความปลอดภัยมีปัญหา',
            'การเชื่อมต่อเครือข่ายไม่เสถียร',
            'ระบบการจัดการผู้ใช้มีปัญหา',
            'การส่งข้อมูลไม่สมบูรณ์',
            'ระบบการตรวจสอบล้มเหลว',
            'การเข้าถึงระบบช้า',
            'ระบบการจัดการสิทธิ์มีปัญหา',
            'การอัปเดตซอฟต์แวร์ล้มเหลว',
            'ระบบการจัดการไฟล์มีปัญหา',
            'การเข้าถึงฐานข้อมูลช้า',
            'ระบบการจัดการการแจ้งเตือนมีปัญหา',
            'การส่งข้อมูลไม่ถูกต้อง',
            'ระบบการจัดการผู้ใช้ช้า',
            'การเข้าถึงระบบไม่เสถียร',
            'ระบบการจัดการสิทธิ์ช้า',
            'การอัปเดตข้อมูลไม่สมบูรณ์',
            'ระบบการจัดการไฟล์ช้า',
        ];

        $baseTitle = $titles[array_rand($titles)];
        return "{$baseTitle} #{$index}";
    }

    private function generateTicketDescription(int $index): string
    {
        $descriptions = [
            'พบปัญหานี้เมื่อใช้งานระบบ ต้องการให้แก้ไขโดยเร็ว',
            'ปัญหานี้เกิดขึ้นบ่อยครั้ง ทำให้การทำงานล่าช้า',
            'ต้องการความช่วยเหลือในการแก้ไขปัญหานี้',
            'ปัญหานี้ส่งผลกระทบต่อการทำงานของทีม',
            'ต้องการให้ตรวจสอบและแก้ไขปัญหานี้',
            'ปัญหานี้เกิดขึ้นหลังจากอัปเดตระบบ',
            'ต้องการให้เพิ่มฟีเจอร์ใหม่เพื่อแก้ปัญหานี้',
            'ปัญหานี้เกิดขึ้นเมื่อมีผู้ใช้หลายคนใช้งานพร้อมกัน',
            'ต้องการให้ปรับปรุงประสิทธิภาพของระบบ',
            'ปัญหานี้เกิดขึ้นเมื่อระบบทำงานหนัก',
        ];

        $baseDescription = $descriptions[array_rand($descriptions)];
        return "{$baseDescription}\n\nรายละเอียดเพิ่มเติม:\n- ปัญหาหมายเลข: {$index}\n- วันที่แจ้ง: " . Carbon::now()->subDays(rand(0, 30))->format('d/m/Y') . "\n- เวลาแจ้ง: " . Carbon::now()->subDays(rand(0, 30))->format('H:i:s');
    }
}
