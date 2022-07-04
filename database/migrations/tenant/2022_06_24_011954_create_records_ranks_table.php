<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records_ranks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('rank_id');
	        $table->unsignedBigInteger('document_id')->nullable();
	        $table->unsignedBigInteger('author_id')->nullable();
	        $table->text('text')->nullable();
            $table->integer('type')->default(\App\Models\Records\Rank::RECORD_RANK_PROMOTION);
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
        Schema::dropIfExists('records_ranks');
    }
}
