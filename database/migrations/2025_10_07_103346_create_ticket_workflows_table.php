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
        Schema::create('ticket_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_step_id')->nullable()->constrained('workflow_steps')->onDelete('set null');
            $table->string('status'); // running, completed, paused, cancelled
            $table->json('completed_steps')->nullable(); // ขั้นตอนที่เสร็จแล้ว
            $table->json('step_data')->nullable(); // ข้อมูลที่เก็บจากแต่ละขั้นตอน
            $table->timestamp('started_at')->nullable(); // เวลาเริ่มต้น
            $table->timestamp('completed_at')->nullable(); // เวลาเสร็จสิ้น
            $table->timestamp('next_action_at')->nullable(); // เวลาที่จะดำเนินการขั้นตอนถัดไป
            $table->timestamps();

            $table->index(['ticket_id', 'status']);
            $table->index('next_action_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_workflows');
    }
};