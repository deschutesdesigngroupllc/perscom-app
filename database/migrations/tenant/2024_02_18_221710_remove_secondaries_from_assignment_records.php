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
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropColumn(['secondary_unit_ids', 'secondary_position_ids', 'secondary_specialty_ids']);
        });

        Schema::dropIfExists('users_positions');
        Schema::dropIfExists('users_specialties');
        Schema::dropIfExists('users_units');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records_assignments', function (Blueprint $table) {
            //
        });
    }
};
