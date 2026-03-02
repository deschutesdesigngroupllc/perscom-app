<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists('Archilex\AdvancedTables\Support\Config')) {
            return;
        }

        Schema::table('filament_filter_sets', function (Blueprint $table) {
            $table->integer(Archilex\AdvancedTables\Support\Config::getTenantColumn())->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        if (! class_exists('Archilex\AdvancedTables\Support\Config')) {
            return;
        }

        Schema::table('filament_filter_sets', function (Blueprint $table) {
            $table->dropColumn(Archilex\AdvancedTables\Support\Config::getTenantColumn());
        });
    }
};
