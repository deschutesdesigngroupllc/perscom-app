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
        Schema::withoutForeignKeyConstraints(function () {
            Schema::table('calendars_tags', function (Blueprint $table) {
                $table->foreignId('tag_id')->change()->constrained('tags')->cascadeOnDelete();
                $table->foreignId('calendar_id')->change()->constrained('calendars')->cascadeOnDelete();
            });

            Schema::table('documents_tags', function (Blueprint $table) {
                $table->foreignId('tag_id')->change()->constrained('tags')->cascadeOnDelete();
                $table->foreignId('document_id')->change()->constrained('documents')->cascadeOnDelete();
            });

            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('calendar_id')->nullable()->change()->constrained('calendars')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('events_registrations', function (Blueprint $table) {
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
                $table->foreignId('event_id')->change()->constrained('events')->cascadeOnDelete();
            });

            Schema::table('events_tags', function (Blueprint $table) {
                $table->foreignId('tag_id')->change()->constrained('tags')->cascadeOnDelete();
                $table->foreignId('event_id')->change()->constrained('events')->cascadeOnDelete();
            });

            Schema::table('forms_tags', function (Blueprint $table) {
                $table->foreignId('tag_id')->change()->constrained('tags')->cascadeOnDelete();
                $table->foreignId('form_id')->change()->constrained('forms')->cascadeOnDelete();
            });

            Schema::table('login_tokens', function (Blueprint $table) {
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('model_has_likes', function (Blueprint $table) {
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('model_has_notifications', function (Blueprint $table) {
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('model_has_statuses', function (Blueprint $table) {
                $table->foreignId('status_id')->change()->constrained('statuses')->cascadeOnDelete();
            });

            Schema::table('records_assignments', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('unit_id')->nullable()->change()->constrained('units')->nullOnDelete();
                $table->foreignId('position_id')->nullable()->change()->constrained('positions')->nullOnDelete();
                $table->foreignId('specialty_id')->nullable()->change()->constrained('specialties')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('records_awards', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('award_id')->nullable()->change()->constrained('awards')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('records_combat', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('records_qualifications', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('qualification_id')->nullable()->change()->constrained('qualifications')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('records_ranks', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('rank_id')->nullable()->change()->constrained('ranks')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('records_service', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
                $table->foreignId('document_id')->nullable()->change()->constrained('documents')->nullOnDelete();
                $table->foreignId('author_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('form_id')->change()->constrained('forms')->cascadeOnDelete();
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('form_id')->nullable()->change()->constrained('forms')->nullOnDelete();
            });

            Schema::table('units_groups', function (Blueprint $table) {
                $table->foreignId('unit_id')->change()->constrained('units')->cascadeOnDelete();
                $table->foreignId('group_id')->change()->constrained('groups')->cascadeOnDelete();
            });

            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('position_id')->nullable()->change()->constrained('positions')->nullOnDelete();
                $table->foreignId('rank_id')->nullable()->change()->constrained('ranks')->nullOnDelete();
                $table->foreignId('specialty_id')->nullable()->change()->constrained('specialties')->nullOnDelete();
                $table->foreignId('status_id')->nullable()->change()->constrained('statuses')->nullOnDelete();
                $table->foreignId('unit_id')->nullable()->change()->constrained('units')->nullOnDelete();
            });

            Schema::table('users_positions', function (Blueprint $table) {
                $table->foreignId('position_id')->change()->constrained('positions')->cascadeOnDelete();
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('users_specialties', function (Blueprint $table) {
                $table->foreignId('specialty_id')->change()->constrained('specialties')->cascadeOnDelete();
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });

            Schema::table('users_tasks', function (Blueprint $table) {
                $table->foreignId('task_id')->change()->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_by_id')->nullable()->change()->constrained('users')->nullOnDelete();
            });

            Schema::table('users_units', function (Blueprint $table) {
                $table->foreignId('unit_id')->change()->constrained('units')->cascadeOnDelete();
                $table->foreignId('user_id')->change()->constrained('users')->cascadeOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::table('calendars_tags', function (Blueprint $table) {
                $table->dropForeign(['tag_id']);
                $table->dropForeign(['calendar_id']);
            });

            Schema::table('documents_tags', function (Blueprint $table) {
                $table->dropForeign(['tag_id']);
                $table->dropForeign(['document_id']);
            });

            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['calendar_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('events_registrations', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['event_id']);
            });

            Schema::table('events_tags', function (Blueprint $table) {
                $table->dropForeign(['tag_id']);
                $table->dropForeign(['event_id']);
            });

            Schema::table('forms_tags', function (Blueprint $table) {
                $table->dropForeign(['tag_id']);
                $table->dropForeign(['form_id']);
            });

            Schema::table('login_tokens', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('model_has_likes', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('model_has_notifications', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('model_has_statuses', function (Blueprint $table) {
                $table->dropForeign(['status_id']);
            });

            Schema::table('records_assignments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['position_id']);
                $table->dropForeign(['specialty_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('records_awards', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['award_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('records_combat', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('records_qualifications', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['qualification_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('records_ranks', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['rank_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('records_service', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['document_id']);
                $table->dropForeign(['author_id']);
            });

            Schema::table('submissions', function (Blueprint $table) {
                $table->dropForeign(['form_id']);
                $table->dropForeign(['user_id']);
            });

            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['form_id']);
            });

            Schema::table('units_groups', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['group_id']);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['position_id']);
                $table->dropForeign(['rank_id']);
                $table->dropForeign(['specialty_id']);
                $table->dropForeign(['status_id']);
                $table->dropForeign(['unit_id']);
            });

            Schema::table('users_positions', function (Blueprint $table) {
                $table->dropForeign(['position_id']);
                $table->dropForeign(['user_id']);
            });

            Schema::table('users_specialties', function (Blueprint $table) {
                $table->dropForeign(['specialty_id']);
                $table->dropForeign(['user_id']);
            });

            Schema::table('users_tasks', function (Blueprint $table) {
                $table->dropForeign(['task_id']);
                $table->dropForeign(['user_id']);
                $table->dropForeign(['assigned_by_id']);
            });

            Schema::table('users_units', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['user_id']);
            });
        });
    }
};
