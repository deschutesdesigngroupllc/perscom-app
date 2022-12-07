<?php

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
        Schema::table('ranks', function (Blueprint $table) {
            $table->integer('order')->after('image')->default(0);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->integer('order')->after('description')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
