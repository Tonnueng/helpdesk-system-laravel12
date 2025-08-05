<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // สร้างบัญชี "เจ้าของธุรกิจ"
        User::create([
            'name' => 'Business Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => 'owner', // เปลี่ยนเป็น 'owner'
            'email_verified_at' => now(),
        ]);

        // สร้างบัญชี "หัวหน้าระบบ"
        User::create([
            'name' => 'System Head',
            'email' => 'head@example.com',
            'password' => Hash::make('password'),
            'role' => 'head', // เพิ่ม role 'head'
            'email_verified_at' => now(),
        ]);

        // สร้างบัญชี "Regular User" (ผู้แจ้งปัญหา)
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // อาจเพิ่มบัญชี 'agent' หรือ 'technician' ถ้ามี
        User::create([
            'name' => 'Support Agent',
            'email' => 'agent@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);
    }
}
