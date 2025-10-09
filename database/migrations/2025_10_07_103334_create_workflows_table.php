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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อ workflow
            $table->text('description')->nullable(); // คำอธิบาย
            $table->string('trigger_type'); // ประเภทการเรียกใช้ (auto, manual, category_based, priority_based)
            $table->json('trigger_conditions')->nullable(); // เงื่อนไขการเรียกใช้ (เช่น category_id, priority_id)
            $table->boolean('is_active')->default(true); // สถานะเปิด/ปิด
            $table->integer('sort_order')->default(0); // ลำดับการเรียง
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};