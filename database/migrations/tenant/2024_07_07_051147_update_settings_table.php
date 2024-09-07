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
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropPrimary('key');
        });

        Schema::table('settings', function (Blueprint $table): void {
            $table->id()->first();
            $table->renameColumn('key', 'name');
            $table->boolean('locked')->default(false);
            $table->string('group')->nullable()->after('id');
            $table->json('payload')->nullable()->after('value');

            $table->unique(['group', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->renameColumn('name', 'key');
            $table->dropColumn(['group', 'payload']);
            $table->dropUnique(['group', 'name']);
            $table->dropTimestamps();
        });
    }
};
