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
            $table->dropForeign('records_assignments_status_id_foreign');
            $table->foreignId('status_id')->nullable()->change()->constrained('statuses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropForeign('records_assignments_status_id_foreign');
            $table->foreignId('status_id')->nullable()->change()->constrained('statuses');
        });
    }
};
