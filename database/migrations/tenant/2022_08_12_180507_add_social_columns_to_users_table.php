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
        Schema::table('users', function (Blueprint $table) {
            $table->after('remember_token', function (Blueprint $table) {
				$table->string('social_id')->nullable();
	            $table->string('social_driver')->nullable();
	            $table->string('social_token')->nullable();
	            $table->string('social_refresh_token')->nullable();
	            $table->string('password')->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['social_id', 'social_driver', 'social_token', 'social_refresh_token']);
            $table->string('password')->change();
        });
    }
};
