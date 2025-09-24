<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างผู้ใช้ทดสอบตาม Role ใหม่
        User::create([
            'name' => 'พนักงาน 1',
            'email' => 'employee1@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '081-234-5678',
            'position' => 'พนักงานขาย',
            'department' => 'ฝ่ายขาย',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'พนักงาน 2',
            'email' => 'employee2@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '082-345-6789',
            'position' => 'พนักงานบัญชี',
            'department' => 'ฝ่ายบัญชี',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'หัวหน้าทีมขาย',
            'email' => 'leader@test.com',
            'password' => Hash::make('password'),
            'role' => 'leader',
            'phone' => '083-456-7890',
            'position' => 'หัวหน้าทีม',
            'department' => 'ฝ่ายขาย',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'ผู้จัดการฝ่าย',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '084-567-8901',
            'position' => 'ผู้จัดการฝ่าย',
            'department' => 'ฝ่ายขาย',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'CEO',
            'email' => 'ceo@test.com',
            'password' => Hash::make('password'),
            'role' => 'ceo',
            'phone' => '085-678-9012',
            'position' => 'ประธานเจ้าหน้าที่บริหาร',
            'department' => 'สำนักงานใหญ่',
            'email_verified_at' => now(),
        ]);
    }
}
