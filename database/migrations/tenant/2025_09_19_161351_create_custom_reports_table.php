<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Padmission\DataLens\Support\Utils;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('data-lens.table_names.custom_reports'), function (Blueprint $table) {
            $table->id();

            $table->uuid('api_uuid')->nullable()->unique();

            if (Utils::isTenantEnabled()) {
                $table->foreignId(Utils::getTenantKeyName());
            }

            $table->string('name');
            $table->string('data_model');

            $table->json('columns');
            $table->json('filters')->nullable();

            // Use JSONB for settings since it's frequently queried with JSON path operators
            if (DB::getDriverName() === 'pgsql') {
                $table->addColumn('jsonb', 'settings')->nullable();
            } else {
                $table->json('settings')->nullable();
            }

            $table->foreignId('creator_id');

            $table->timestamps();

            $table->index('api_uuid');
        });

        Schema::create(config('data-lens.table_names.custom_report_user'), function (Blueprint $table) {
            $table->foreignId('custom_report_id')
                ->constrained(config('data-lens.table_names.custom_reports'))
                ->cascadeOnDelete();

            $table->foreignId('user_id');

            $table->primary(['custom_report_id', 'user_id']);
        });

        Schema::create(config('data-lens.table_names.custom_report_schedules'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_report_id')->constrained(config('data-lens.table_names.custom_reports'))->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('status');
            $table->integer('runs_count')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('time');
            $table->text('rrule')->nullable();
            $table->string('timezone')->default(config('app.timezone'));
            $table->string('export_format');
            $table->string('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->dateTime('last_success_at')->nullable();
            $table->dateTime('last_error_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->foreignId('creator_id')->nullable();

            if (Utils::isTenantEnabled()) {
                $table->foreignId(Utils::getTenantKeyName())->nullable();
            }

            // Use JSONB for settings since it's frequently queried with JSON path operators
            if (DB::getDriverName() === 'pgsql') {
                $table->addColumn('jsonb', 'settings')->nullable();
            } else {
                $table->json('settings')->nullable();
            }
            $table->timestamps();
        });

        Schema::create(config('data-lens.table_names.custom_report_schedule_history'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_schedule_id')->constrained(config('data-lens.table_names.custom_report_schedules'))->cascadeOnDelete();
            $table->timestamp('executed_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->integer('recipient_count')->default(0);
            $table->integer('file_size')->nullable()->comment('Size in bytes');
            $table->integer('processing_time')->nullable()->comment('Time in milliseconds');
            $table->boolean('email_sent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create(config('data-lens.table_names.custom_report_schedule_recipients'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_schedule_id')->constrained(config('data-lens.table_names.custom_report_schedules'))->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('data-lens.table_names.custom_report_schedule_recipients'));
        Schema::dropIfExists(config('data-lens.table_names.custom_report_schedule_history'));
        Schema::dropIfExists(config('data-lens.table_names.custom_report_schedules'));
        Schema::dropIfExists(config('data-lens.table_names.custom_report_user'));
        Schema::dropIfExists(config('data-lens.table_names.custom_reports'));
    }
};
