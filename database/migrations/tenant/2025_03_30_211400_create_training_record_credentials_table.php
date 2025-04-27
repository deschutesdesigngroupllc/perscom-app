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
        Schema::create('records_trainings_credentials', function (Blueprint $table) {
            $table->foreignId('training_record_id')->nullable()->constrained('records_trainings')->cascadeOnDelete();
            $table->foreignId('credential_id')->nullable()->constrained('credentials')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records_trainings_credentials');
    }
};
