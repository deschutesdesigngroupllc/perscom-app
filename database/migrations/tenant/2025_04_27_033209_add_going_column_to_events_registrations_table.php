<?php

declare(strict_types=1);

use App\Models\Enums\EventRegistrationStatus;
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
            $table->string('status')->after('event_id')->default(EventRegistrationStatus::Going)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_registrations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
