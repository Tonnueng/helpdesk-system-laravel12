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
        Schema::table('tickets', function (Blueprint $table) {
            // เพิ่มคอลัมน์สำหรับเก็บข้อมูลรูปภาพ
            $table->text('images')->nullable()->after('description')->comment('JSON array of image file paths');
            $table->string('primary_image')->nullable()->after('images')->comment('Primary/thumbnail image path');
            $table->integer('image_count')->default(0)->after('primary_image')->comment('Number of images attached');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['images', 'primary_image', 'image_count']);
        });
    }
};
