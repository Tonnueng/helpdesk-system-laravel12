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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ชื่อขั้นตอน
            $table->text('description')->nullable(); // คำอธิบายขั้นตอน
            $table->integer('step_order'); // ลำดับขั้นตอน
            $table->string('action_type'); // ประเภทการกระทำ (assign, status_change, notification, auto_reply, escalation)
            $table->json('action_config')->nullable(); // การตั้งค่าการกระทำ (เช่น user_id, status_id, message)
            $table->json('conditions')->nullable(); // เงื่อนไขการทำงาน (เช่น time_delay, user_response)
            $table->boolean('is_required')->default(true); // ขั้นตอนบังคับหรือไม่
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};