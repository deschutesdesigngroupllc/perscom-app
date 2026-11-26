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
        Schema::table('forms', function (Blueprint $table): void {
            $table->after('slug', function (Blueprint $table): void {
                $table->text('success_message')->nullable();
                $table->boolean('is_public')->default(false);
            });
        });

        Schema::table('fields', function (Blueprint $table): void {
            $table->after('required', function (Blueprint $table): void {
                $table->boolean('readonly')->default(false);
                $table->boolean('disabled')->default(false);
            });
            $table->text('text')->after('help')->nullable();
            $table->string('key')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->dropColumn('success_message', 'is_public');
        });

        Schema::table('fields', function (Blueprint $table): void {
            $table->dropColumn('readonly', 'disabled', 'text', 'key');
        });
    }
};
