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
        Schema::table('model_has_notifications', function (Blueprint $table) {
            $table->dropForeign('model_has_notifications_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::table('model_has_notifications', function (Blueprint $table) {
            $table->after('model_id', function (Blueprint $table) {
                $table->nullableMorphs('group');
                $table->nullableMorphs('unit');
                $table->nullableMorphs('user');
                $table->string('event')->nullable();
                $table->text('subject')->nullable();
                $table->text('message')->nullable();
                $table->json('channels')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
