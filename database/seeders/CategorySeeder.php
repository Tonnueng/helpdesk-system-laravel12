<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // หมวดหมู่หลักและย่อยตามที่กำหนด
        $categories = [
            // Operation & Production
            [
                'name' => 'Operation & Production',
                'parent_category' => null,
                'description' => 'ปัญหาด้านการผลิตและการดำเนินงาน',
                'sort_order' => 1,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'การผลิต', 'description' => 'ปัญหาด้านกระบวนการผลิต', 'sort_order' => 1],
                    ['name' => 'การแพ็ค', 'description' => 'ปัญหาด้านการบรรจุภัณฑ์', 'sort_order' => 2],
                    ['name' => 'การจัดส่ง', 'description' => 'ปัญหาด้านการขนส่งและจัดส่ง', 'sort_order' => 3],
                    ['name' => 'คลังสินค้า', 'description' => 'ปัญหาด้านการจัดการคลังสินค้า', 'sort_order' => 4],
                ]
            ],
            
            // Sales & Customer
            [
                'name' => 'Sales & Customer',
                'parent_category' => null,
                'description' => 'ปัญหาด้านการขายและลูกค้า',
                'sort_order' => 2,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'คำสั่งซื้อ', 'description' => 'ปัญหาด้านการรับคำสั่งซื้อ', 'sort_order' => 1],
                    ['name' => 'การคืนเงิน', 'description' => 'ปัญหาด้านการคืนเงิน', 'sort_order' => 2],
                    ['name' => 'การร้องเรียนจากลูกค้า', 'description' => 'ปัญหาจากการร้องเรียนลูกค้า', 'sort_order' => 3],
                ]
            ],
            
            // Marketing & Ads
            [
                'name' => 'Marketing & Ads',
                'parent_category' => null,
                'description' => 'ปัญหาด้านการตลาดและโฆษณา',
                'sort_order' => 3,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'ประสิทธิภาพโฆษณา', 'description' => 'ปัญหาด้านประสิทธิภาพการโฆษณา', 'sort_order' => 1],
                    ['name' => 'ปัญหาแคมเปญ', 'description' => 'ปัญหาด้านแคมเปญการตลาด', 'sort_order' => 2],
                    ['name' => 'ความล่าช้าด้านคอนเทนต์', 'description' => 'ปัญหาด้านการผลิตคอนเทนต์', 'sort_order' => 3],
                ]
            ],
            
            // Finance & Accounting
            [
                'name' => 'Finance & Accounting',
                'parent_category' => null,
                'description' => 'ปัญหาด้านการเงินและบัญชี',
                'sort_order' => 4,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'กระแสเงินสด', 'description' => 'ปัญหาด้านการจัดการกระแสเงินสด', 'sort_order' => 1],
                    ['name' => 'ปัญหาการชำระเงิน', 'description' => 'ปัญหาด้านการชำระเงิน', 'sort_order' => 2],
                    ['name' => 'การควบคุมต้นทุน', 'description' => 'ปัญหาด้านการควบคุมต้นทุน', 'sort_order' => 3],
                ]
            ],
            
            // People & HR
            [
                'name' => 'People & HR',
                'parent_category' => null,
                'description' => 'ปัญหาด้านทรัพยากรบุคคล',
                'sort_order' => 5,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'การขาด/ลา/สาย', 'description' => 'ปัญหาด้านการขาดงาน ลา และมาสาย', 'sort_order' => 1],
                    ['name' => 'ประสิทธิภาพการทำงาน', 'description' => 'ปัญหาด้านประสิทธิภาพการทำงาน', 'sort_order' => 2],
                    ['name' => 'การสื่อสาร', 'description' => 'ปัญหาด้านการสื่อสารภายในองค์กร', 'sort_order' => 3],
                ]
            ],
            
            // IT & Systems
            [
                'name' => 'IT & Systems',
                'parent_category' => null,
                'description' => 'ปัญหาด้านเทคโนโลยีสารสนเทศ',
                'sort_order' => 6,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'ปัญหาระบบ', 'description' => 'ปัญหาด้านระบบคอมพิวเตอร์', 'sort_order' => 1],
                    ['name' => 'ฮาร์ดแวร์', 'description' => 'ปัญหาด้านอุปกรณ์ฮาร์ดแวร์', 'sort_order' => 2],
                    ['name' => 'ความปลอดภัย', 'description' => 'ปัญหาด้านความปลอดภัยข้อมูล', 'sort_order' => 3],
                ]
            ],
            
            // Supplier & Partner
            [
                'name' => 'Supplier & Partner',
                'parent_category' => null,
                'description' => 'ปัญหาด้านซัพพลายเออร์และพาร์ทเนอร์',
                'sort_order' => 7,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'ความล่าช้า', 'description' => 'ปัญหาด้านความล่าช้าจากซัพพลายเออร์', 'sort_order' => 1],
                    ['name' => 'คุณภาพสินค้า', 'description' => 'ปัญหาด้านคุณภาพสินค้าจากซัพพลายเออร์', 'sort_order' => 2],
                    ['name' => 'ราคาผันผวน', 'description' => 'ปัญหาด้านราคาที่ผันผวน', 'sort_order' => 3],
                ]
            ],
            
            // Strategy & Management
            [
                'name' => 'Strategy & Management',
                'parent_category' => null,
                'description' => 'ปัญหาด้านกลยุทธ์และการจัดการ',
                'sort_order' => 8,
                'is_active' => true,
                'subcategories' => [
                    ['name' => 'นโยบาย', 'description' => 'ปัญหาด้านนโยบายองค์กร', 'sort_order' => 1],
                    ['name' => 'การจัดสรร KPI', 'description' => 'ปัญหาด้านการจัดสรร KPI', 'sort_order' => 2],
                    ['name' => 'ความล่าช้าในการตัดสินใจ', 'description' => 'ปัญหาด้านความล่าช้าในการตัดสินใจ', 'sort_order' => 3],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            // สร้างหมวดหมู่หลัก
            $mainCategory = Category::create([
                'name' => $categoryData['name'],
                'parent_category' => $categoryData['parent_category'],
                'description' => $categoryData['description'],
                'sort_order' => $categoryData['sort_order'],
                'is_active' => $categoryData['is_active'],
            ]);

            // สร้างหมวดหมู่ย่อย
            foreach ($categoryData['subcategories'] as $subcategoryData) {
                Category::create([
                    'name' => $subcategoryData['name'],
                    'parent_category' => $mainCategory->name,
                    'description' => $subcategoryData['description'],
                    'sort_order' => $subcategoryData['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
