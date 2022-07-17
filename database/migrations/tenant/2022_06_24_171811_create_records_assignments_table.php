<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records_assignments', function (Blueprint $table) {
            $table->id();
	        $table->unsignedBigInteger('user_id');
	        $table->unsignedBigInteger('unit_id');
	        $table->unsignedBigInteger('position_id');
	        $table->unsignedBigInteger('specialty_id');
	        $table->unsignedBigInteger('document_id')->nullable();
	        $table->unsignedBigInteger('author_id')->nullable();
	        $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records_assignments');
    }
}
