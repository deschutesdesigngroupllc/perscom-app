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
        Schema::create('mail', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('content');
            $table->json('recipients');
            $table->json('links')->nullable();
            $table->boolean('send_now')->default(true);
            $table->dateTime('send_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail');
    }
};
