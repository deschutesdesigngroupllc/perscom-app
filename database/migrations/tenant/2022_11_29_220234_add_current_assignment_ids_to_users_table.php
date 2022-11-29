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
        	$table->after('email_verified_at', function (Blueprint $table) {
		        $table->unsignedBigInteger('position_id')->nullable();
		        $table->unsignedBigInteger('rank_id')->nullable();
		        $table->unsignedBigInteger('specialty_id')->nullable();
		        $table->unsignedBigInteger('status_id')->nullable();
		        $table->unsignedBigInteger('unit_id')->nullable();
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
	        $table->dropColumn('position_id');
	        $table->dropColumn('rank_id');
	        $table->dropColumn('specialty_id');
            $table->dropColumn('status_id');
	        $table->dropColumn('unit_id');
        });
    }
};
