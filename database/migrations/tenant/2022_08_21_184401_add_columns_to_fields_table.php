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
        Schema::table('forms', function (Blueprint $table) {
            $table->after('slug', function (Blueprint $table) {
				$table->boolean('is_public')->default(false);
            });
        });

	    Schema::table('fields', function (Blueprint $table) {
		    $table->after('required', function (Blueprint $table) {
			    $table->boolean('readonly')->default(false);
			    $table->boolean('disabled')->default(false);
		    });
		    $table->text('text')->after('help')->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });

	    Schema::table('fields', function (Blueprint $table) {
		    $table->dropColumn('readonly');
		    $table->dropColumn('disabled');
		    $table->dropColumn('text');
	    });
    }
};
