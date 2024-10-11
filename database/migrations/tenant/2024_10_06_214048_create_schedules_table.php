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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('repeatable');
            $table->timestamp('start');
            $table->integer('duration');
            $table->text('frequency');
            $table->integer('interval')->default(1);
            $table->string('end_type')->nullable();
            $table->integer('count')->nullable();
            $table->timestamp('until')->nullable();
            $table->json('by_day')->nullable();
            $table->json('by_month')->nullable();
            $table->json('by_set_position')->nullable();
            $table->json('by_month_day')->nullable();
            $table->json('by_year_day')->nullable();
            $table->string('rrule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
