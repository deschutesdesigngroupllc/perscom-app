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
        Schema::table('calendars', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('forms', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('submissions', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('mail', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('announcements', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('awards', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('groups', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('positions', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('qualifications', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('ranks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('specialties', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('statuses', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('tasks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_assignments', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_awards', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_combat', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_qualifications', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_ranks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_service', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('webhooks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('attachments', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('fields', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('images', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
