<?php

declare(strict_types=1);

use App\Models\Enums\FieldOptionsType;
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
        Schema::table('fields', function (Blueprint $table) {
            $table->after('options', function (Blueprint $table) {
                $table->string('options_type')->default(FieldOptionsType::Array->value)->nullable();
                $table->string('options_model')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn(['options_type', 'options_model']);
        });
    }
};
