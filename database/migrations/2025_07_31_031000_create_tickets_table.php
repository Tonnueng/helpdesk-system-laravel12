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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้แจ้งปัญหา, ลบผู้ใช้ ปัญหาจะถูกลบด้วย
            $table->foreignId('category_id')->constrained()->onDelete('restrict'); // ประเภทปัญหา
            $table->foreignId('priority_id')->constrained()->onDelete('restrict'); // ระดับความสำคัญ
            $table->foreignId('status_id')->constrained()->onDelete('restrict'); // สถานะปัญหา
            $table->string('title'); // หัวข้อ
            $table->text('description'); // รายละเอียด
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null'); // ผู้ที่ได้รับมอบหมาย, กำหนดเป็น null เมื่อผู้ดูแลถูกลบ
            $table->timestamp('reported_at')->nullable(); // วันที่และเวลาที่พบปัญหา
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};