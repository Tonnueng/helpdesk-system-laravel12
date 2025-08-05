<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(['name' => 'New']);
        Status::create(['name' => 'In Progress']);
        Status::create(['name' => 'Pending']);
        Status::create(['name' => 'Resolved']);
        Status::create(['name' => 'Closed']);
        Status::create(['name' => 'Rejected']);
    }
}
