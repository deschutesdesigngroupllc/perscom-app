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
        Schema::table('events_registrations', function (Blueprint $table) {
            $table->foreignId('event_occurrence_id')->nullable()->after('event_id')->constrained('events_occurrences')->cascadeOnDelete();

            $table->unique(['event_id', 'user_id', 'event_occurrence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_registrations', function (Blueprint $table) {
            $table->dropUnique(['event_id', 'user_id', 'event_occurrence_id']);
            $table->dropConstrainedForeignId('event_occurrence_id');
        });
    }
};
