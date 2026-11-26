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
        Schema::create('images', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('filename');
            $table->morphs('model');
            $table->string('path');
            $table->timestamps();
        });

        Schema::table('ranks', function (Blueprint $table): void {
            $table->dropColumn('image');
        });

        Schema::table('awards', function (Blueprint $table): void {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');

        Schema::table('ranks', function (Blueprint $table): void {
            $table->string('image')->nullable();
        });

        Schema::table('awards', function (Blueprint $table): void {
            $table->string('image')->nullable();
        });
    }
};
