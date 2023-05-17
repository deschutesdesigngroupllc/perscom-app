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
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->json('secondary_position_ids')->nullable()->after('position_id');
            $table->json('secondary_specialty_ids')->nullable()->after('specialty_id');
            $table->json('secondary_unit_ids')->nullable()->after('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropColumn(['secondary_position_ids', 'secondary_specialty_ids', 'secondary_unit_ids']);
        });
    }
};
