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
        Schema::create('automations_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->string('trigger');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->string('status');
            $table->text('condition_expression')->nullable();
            $table->boolean('condition_result')->nullable();
            $table->json('context')->nullable();
            $table->json('action_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('execution_time_ms')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automations_logs');
    }
};
