<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // เปลี่ยน role enum จากเดิมเป็น Role ใหม่
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['employee', 'leader', 'manager', 'ceo'])->default('employee')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เปลี่ยนกลับเป็น Role เดิม
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'agent', 'head', 'owner'])->default('user')->change();
        });
    }
};
