<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automations', function (Blueprint $table): void {
            $table->string('run_limit')->default('always')->after('priority');
        });

        Schema::create('automation_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->date('run_on');
            $table->timestamps();

            $table->index(['automation_id', 'subject_type', 'subject_id', 'run_on'], 'automation_runs_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_runs');

        Schema::table('automations', function (Blueprint $table): void {
            $table->dropColumn('run_limit');
        });
    }
};
