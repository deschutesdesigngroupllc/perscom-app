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
        Schema::create('forms_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('form_id');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('name')->change();
            $table->dropColumn('slug', 'type', 'order_column');
        });

        Schema::dropIfExists('taggables');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms_tags');

        Schema::table('tags', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('slug');
            $table->string('type')->nullable();
            $table->integer('order_column')->nullable();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->morphs('taggable');
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }
};
