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
        Schema::table('ticket_updates', function (Blueprint $table) {
            $table->string('type')->default('comment')->after('comment');
            $table->string('old_values')->nullable()->after('type');
            $table->string('new_values')->nullable()->after('old_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_updates', function (Blueprint $table) {
            $table->dropColumn(['type', 'old_values', 'new_values']);
        });
    }
};
