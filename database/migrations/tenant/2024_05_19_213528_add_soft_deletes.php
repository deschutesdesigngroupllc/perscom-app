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
        Schema::table('calendars', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('awards', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('qualifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ranks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('specialties', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_assignments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_awards', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_combat', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_ranks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_service', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('webhooks', function (Blueprint $table) {
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
