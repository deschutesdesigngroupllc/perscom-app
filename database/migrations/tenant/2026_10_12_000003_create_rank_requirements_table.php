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
        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rank_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('min_days_in_grade')->nullable();
            $table->unsignedInteger('min_days_in_service')->nullable();
            $table->unsignedInteger('min_events_attended_90d')->nullable();
            $table->json('required_qualification_ids')->nullable();
            $table->json('required_credential_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_requirements');
    }
};
