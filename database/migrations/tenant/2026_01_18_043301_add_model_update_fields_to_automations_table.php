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
        Schema::table('automations', function (Blueprint $table) {
            $table->string('model_update_target')->nullable()->after('message_recipients_expression');
            $table->string('model_update_lookup_type')->nullable()->after('model_update_target');
            $table->text('model_update_lookup_expression')->nullable()->after('model_update_lookup_type');
            $table->json('model_update_lookup_conditions')->nullable()->after('model_update_lookup_expression');
            $table->json('model_update_fields')->nullable()->after('model_update_lookup_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->dropColumn([
                'model_update_target',
                'model_update_lookup_type',
                'model_update_lookup_expression',
                'model_update_lookup_conditions',
                'model_update_fields',
            ]);
        });
    }
};
