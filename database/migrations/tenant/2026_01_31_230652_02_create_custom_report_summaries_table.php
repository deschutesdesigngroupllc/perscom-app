<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('data-lens.table_names.custom_report_summaries', 'custom_report_summaries');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_report_id')
                ->constrained(config('data-lens.table_names.custom_reports', 'custom_reports'))
                ->cascadeOnDelete();
            $table->string('name');
            $table->json('configuration');
            $table->enum('processing_strategy', ['sql', 'collection', 'hybrid', 'auto'])
                ->default('auto');
            $table->boolean('cache_enabled')->default(true);
            $table->timestamp('cached_at')->nullable();
            $table->json('cached_data')->nullable();

            $table->json('widget_configurations')->nullable();

            $table->timestamps();

            $table->index(['custom_report_id', 'name'], 'crs_report_name_unique');
        });
    }
};
