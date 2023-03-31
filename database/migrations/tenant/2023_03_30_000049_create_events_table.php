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
            $table->boolean('all_day')->default(false);
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->boolean('repeats')->default(false);
            $table->text('frequency')->nullable();
            $table->integer('interval')->default(1);
            $table->text('end_type')->nullable();
            $table->integer('count')->nullable();
            $table->timestamp('until')->nullable();
            $table->json('repeat_day')->nullable();
            $table->integer('repeat_month_day')->nullable();
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
