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
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('model_type')->nullable()->change();
            $table->unsignedBigInteger('model_id')->nullable()->change();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->string('model_type')->nullable()->change();
            $table->unsignedBigInteger('model_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
