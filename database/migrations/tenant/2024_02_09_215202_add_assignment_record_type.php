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
            $table->enum('type', ['primary', 'secondary'])->nullable()->after('author_id')->default('primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
