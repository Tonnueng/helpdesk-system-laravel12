<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComprehensiveUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ลบ users เก่า (ยกเว้น admin)
        User::where('email', '!=', 'admin@test.com')->delete();

        // 👑 CEO (1 คน)
        User::create([
            'name' => 'CEO',
            'email' => 'ceo@test.com',
            'password' => Hash::make('password'),
            'role' => 'ceo',
            'department' => 'สำนักงานใหญ่',
            'position' => 'CEO',
            'phone' => '081-000-0001',
        ]);

        // 👨‍💼 ผู้จัดการ (1 คน)
        User::create([
            'name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department' => 'ฝ่ายขาย',
            'position' => 'ผู้จัดการฝ่ายขาย',
            'phone' => '081-000-0002',
        ]);

        // 👨‍💼 หัวหน้าทีม (9 คน)
        $leaders = [
            [
                'name' => 'Leader Operation',
                'email' => 'leader.operation@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายผลิต',
                'position' => 'หัวหน้าฝ่ายผลิต',
                'phone' => '081-000-0011',
                'responsible_for' => 'Operation & Production',
            ],
            [
                'name' => 'Leader Sales',
                'email' => 'leader.sales@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายขาย',
                'position' => 'หัวหน้าฝ่ายขาย',
                'phone' => '081-000-0012',
                'responsible_for' => 'Sales & Customer',
            ],
            [
                'name' => 'Leader Marketing',
                'email' => 'leader.marketing@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายการตลาด',
                'position' => 'หัวหน้าฝ่ายการตลาด',
                'phone' => '081-000-0013',
                'responsible_for' => 'Marketing & Ads',
            ],
            [
                'name' => 'Leader Finance',
                'email' => 'leader.finance@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายการเงิน',
                'position' => 'หัวหน้าฝ่ายการเงิน',
                'phone' => '081-000-0014',
                'responsible_for' => 'Finance & Accounting',
            ],
            [
                'name' => 'Leader HR',
                'email' => 'leader.hr@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายทรัพยากรบุคคล',
                'position' => 'หัวหน้าฝ่ายทรัพยากรบุคคล',
                'phone' => '081-000-0015',
                'responsible_for' => 'People & HR',
            ],
            [
                'name' => 'Leader IT',
                'email' => 'leader.it@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายเทคโนโลยี',
                'position' => 'หัวหน้าฝ่ายเทคโนโลยี',
                'phone' => '081-000-0016',
                'responsible_for' => 'IT & Systems',
            ],
            [
                'name' => 'Leader Supplier',
                'email' => 'leader.supplier@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายจัดซื้อ',
                'position' => 'หัวหน้าฝ่ายจัดซื้อ',
                'phone' => '081-000-0017',
                'responsible_for' => 'Supplier & Partner',
            ],
            [
                'name' => 'Leader Strategy',
                'email' => 'leader.strategy@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายกลยุทธ์',
                'position' => 'หัวหน้าฝ่ายกลยุทธ์',
                'phone' => '081-000-0018',
                'responsible_for' => 'Strategy & Management',
            ],
            [
                'name' => 'Leader General',
                'email' => 'leader@test.com',
                'role' => 'leader',
                'department' => 'ฝ่ายขาย',
                'position' => 'หัวหน้าทีมขาย',
                'phone' => '081-000-0019',
                'responsible_for' => 'Sales & Customer',
            ],
        ];

        foreach ($leaders as $leader) {
            User::create([
                'name' => $leader['name'],
                'email' => $leader['email'],
                'password' => Hash::make('password'),
                'role' => $leader['role'],
                'department' => $leader['department'],
                'position' => $leader['position'],
                'phone' => $leader['phone'],
            ]);
        }

        // 👷 พนักงาน (13 คน)
        $employees = [
            [
                'name' => 'Employee Sales 1',
                'email' => 'employee1@test.com',
                'department' => 'ฝ่ายขาย',
                'position' => 'พนักงานขาย',
                'phone' => '081-000-0021',
            ],
            [
                'name' => 'Employee Accounting',
                'email' => 'employee2@test.com',
                'department' => 'ฝ่ายบัญชี',
                'position' => 'พนักงานบัญชี',
                'phone' => '081-000-0022',
            ],
            [
                'name' => 'Employee Operation 1',
                'email' => 'emp.operation1@test.com',
                'department' => 'ฝ่ายผลิต',
                'position' => 'พนักงานผลิต',
                'phone' => '081-000-0023',
            ],
            [
                'name' => 'Employee Operation 2',
                'email' => 'emp.operation2@test.com',
                'department' => 'ฝ่ายผลิต',
                'position' => 'พนักงานแพ็ค',
                'phone' => '081-000-0024',
            ],
            [
                'name' => 'Employee Sales 2',
                'email' => 'emp.sales1@test.com',
                'department' => 'ฝ่ายขาย',
                'position' => 'พนักงานขาย',
                'phone' => '081-000-0025',
            ],
            [
                'name' => 'Employee Sales 3',
                'email' => 'emp.sales2@test.com',
                'department' => 'ฝ่ายขาย',
                'position' => 'พนักงานบริการลูกค้า',
                'phone' => '081-000-0026',
            ],
            [
                'name' => 'Employee Marketing',
                'email' => 'emp.marketing1@test.com',
                'department' => 'ฝ่ายการตลาด',
                'position' => 'พนักงานการตลาด',
                'phone' => '081-000-0027',
            ],
            [
                'name' => 'Employee Finance',
                'email' => 'emp.finance1@test.com',
                'department' => 'ฝ่ายการเงิน',
                'position' => 'พนักงานบัญชี',
                'phone' => '081-000-0028',
            ],
            [
                'name' => 'Employee HR',
                'email' => 'emp.hr1@test.com',
                'department' => 'ฝ่ายทรัพยากรบุคคล',
                'position' => 'พนักงานทรัพยากรบุคคล',
                'phone' => '081-000-0029',
            ],
            [
                'name' => 'Employee IT 1',
                'email' => 'emp.it1@test.com',
                'department' => 'ฝ่ายเทคโนโลยี',
                'position' => 'พนักงานไอที',
                'phone' => '081-000-0030',
            ],
            [
                'name' => 'Employee IT 2',
                'email' => 'emp.it2@test.com',
                'department' => 'ฝ่ายเทคโนโลยี',
                'position' => 'พนักงานระบบ',
                'phone' => '081-000-0031',
            ],
            [
                'name' => 'Employee Supplier',
                'email' => 'emp.supplier1@test.com',
                'department' => 'ฝ่ายจัดซื้อ',
                'position' => 'พนักงานจัดซื้อ',
                'phone' => '081-000-0032',
            ],
            [
                'name' => 'Employee Strategy',
                'email' => 'emp.strategy1@test.com',
                'department' => 'ฝ่ายกลยุทธ์',
                'position' => 'พนักงานกลยุทธ์',
                'phone' => '081-000-0033',
            ],
        ];

        foreach ($employees as $employee) {
            User::create([
                'name' => $employee['name'],
                'email' => $employee['email'],
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => $employee['department'],
                'position' => $employee['position'],
                'phone' => $employee['phone'],
            ]);
        }

        $this->command->info('Created comprehensive users successfully!');
        $this->command->info('CEO: 1 person');
        $this->command->info('Manager: 1 person');
        $this->command->info('Leaders: 9 people');
        $this->command->info('Employees: 13 people');
        $this->command->info('Total: 24 people');
    }
}
