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
        Schema::table('statuses', function (Blueprint $table) {
            $table->renameColumn('color', 'text_color');
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->string('text_color')->nullable()->change();
            $table->string('bg_color')->after('text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->renameColumn('text_color', 'color');
            $table->dropColumn('bg_color');
        });
    }
};
