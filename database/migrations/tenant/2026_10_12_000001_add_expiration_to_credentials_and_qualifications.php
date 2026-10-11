<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credentials', function (Blueprint $table): void {
            $table->unsignedInteger('validity_days')->nullable()->after('issuer_id');
        });

        Schema::table('qualifications', function (Blueprint $table): void {
            $table->unsignedInteger('validity_days')->nullable()->after('description');
        });

        Schema::table('records_trainings_credentials', function (Blueprint $table): void {
            $table->id()->first();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
        });

        Schema::table('records_qualifications', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->index()->after('text');
        });

        DB::table('records_trainings_credentials')
            ->join('records_trainings', 'records_trainings.id', '=', 'records_trainings_credentials.training_record_id')
            ->update(['records_trainings_credentials.issued_at' => DB::raw('records_trainings.created_at')]);
    }

    public function down(): void
    {
        Schema::table('records_qualifications', function (Blueprint $table): void {
            $table->dropIndex(['expires_at']);
            $table->dropColumn('expires_at');
        });

        Schema::table('records_trainings_credentials', function (Blueprint $table): void {
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['id', 'issued_at', 'expires_at']);
        });

        Schema::table('qualifications', function (Blueprint $table): void {
            $table->dropColumn('validity_days');
        });

        Schema::table('credentials', function (Blueprint $table): void {
            $table->dropColumn('validity_days');
        });
    }
};
