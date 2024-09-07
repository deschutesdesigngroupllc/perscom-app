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
        Schema::table('groups', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('hidden');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('hidden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('icon');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
