<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate->Database->Console->Seeds->WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // ปิดบรรทัดนี้ หรือลบออก ถ้าไม่ต้องการสร้าง user สุ่ม

        $this->call([
            CategorySeeder::class,
            PrioritySeeder::class,
            StatusSeeder::class,
            AdminUserSeeder::class, // เรียกใช้ AdminUserSeeder หลังจาก Seeder อื่นๆ
            TestUserSeeder::class,
        ]);
    }
}