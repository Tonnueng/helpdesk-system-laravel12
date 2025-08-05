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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade'); // อ้างอิงถึงปัญหา
            $table->string('filename'); // ชื่อไฟล์จริง
            $table->string('filepath'); // Path สำหรับเก็บไฟล์
            $table->string('mime_type')->nullable(); // ประเภทไฟล์ (เช่น image/jpeg)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
