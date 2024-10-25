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
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->after('id');
            $table->unsignedBigInteger('recipient_id')->after('sender_id');

            // Index for performance
            $table->index('sender_id');
            $table->index('recipient_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['recipient_id']);
            $table->dropColumn(['sender_id', 'recipient_id']);
        });
    }
};
