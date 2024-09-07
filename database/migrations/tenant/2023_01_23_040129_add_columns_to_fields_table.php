<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->after('type', function (Blueprint $table) {
                $table->string('nova_type')->nullable();
                $table->string('cast')->nullable();
            });

            $table->dropColumn('disabled', 'text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('nova_type', 'cast');
            $table->boolean('disabled')->default(false);
            $table->text('text')->nullable();
        });
    }
};
