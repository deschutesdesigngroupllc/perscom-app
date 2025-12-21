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
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });

        Schema::table('records_awards', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });

        Schema::table('records_combat', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });

        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });

        Schema::table('records_ranks', function (Blueprint $table) {
            $table->json('data')->nullable()->after('type');
        });

        Schema::table('records_service', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });

        Schema::table('records_trainings', function (Blueprint $table) {
            $table->json('data')->nullable()->after('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            //
        });
    }
};
