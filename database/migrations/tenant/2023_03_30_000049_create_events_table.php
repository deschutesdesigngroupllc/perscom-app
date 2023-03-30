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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('calendar_id');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('location')->nullable();
            $table->text('url')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('repeats')->default(false);
            $table->text('frequency')->nullable();
            $table->integer('interval')->default(1);
            $table->integer('count')->nullable();
            $table->json('by_day')->nullable();
            $table->json('by_month')->nullable();
            $table->json('by_month_day')->nullable();
            $table->integer('by_set_position')->nullable();
            $table->text('rrule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
