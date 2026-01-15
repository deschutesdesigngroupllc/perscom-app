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
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger');
            $table->text('condition')->nullable();
            $table->string('action_type')->default('webhook');
            $table->foreignId('webhook_id')->nullable()->constrained()->nullOnDelete();
            $table->json('webhook_payload_template')->nullable();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message_template')->nullable();
            $table->text('message_recipients_expression')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();

            $table->index('trigger');
            $table->index('enabled');
            $table->index('action_type');
            $table->index(['trigger', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automations');
    }
};
