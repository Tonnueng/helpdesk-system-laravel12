<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างผู้ใช้ทดสอบ
        User::create([
            'name' => 'ผู้แจ้งปัญหา',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'เจ้าหน้าที่ 1',
            'email' => 'agent1@test.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'เจ้าหน้าที่ 2',
            'email' => 'agent2@test.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'หัวหน้าระบบ',
            'email' => 'head@test.com',
            'password' => Hash::make('password'),
            'role' => 'head',
            'email_verified_at' => now(),
        ]);
    }
} 