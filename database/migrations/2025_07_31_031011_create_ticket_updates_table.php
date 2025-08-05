<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade'); // อ้างอิงถึงปัญหา
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ที่ทำการอัปเดต (ผู้แจ้ง หรือ ผู้ดูแล)
            $table->text('comment')->nullable(); // ข้อความอัปเดต
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('set null'); // สถานะใหม่ (ถ้ามีการเปลี่ยน)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_updates');
    }
};
