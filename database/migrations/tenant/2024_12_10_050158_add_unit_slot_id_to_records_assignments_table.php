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
            $table->after('specialty_id', function (Blueprint $table) {
                $table->foreignId('unit_slot_id')->nullable()->constrained('units_slots')->nullOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropForeign('records_assignments_unit_slot_id_foreign');
            $table->dropColumn('unit_slot_id');
        });
    }
};
