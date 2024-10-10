<?php

declare(strict_types=1);

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
        Schema::table('events', function (Blueprint $table) {
            $table->after('registration_deadline', function (Blueprint $table) {
                $table->boolean('notifications_enabled')->default(true);
                $table->string('notifications_interval')->nullable();
                $table->string('notifications_channels')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['notifications_enabled', 'notifications_interval']);
        });
    }
};
