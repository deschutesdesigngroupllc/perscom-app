<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists('Archilex\AdvancedTables\Support\Config')) {
            return;
        }

        Schema::create('filament_filter_set_user', function (Blueprint $table) {
            $userClass = Archilex\AdvancedTables\Support\Config::getUser();
            $user = new $userClass;

            $table->id();

            $table->foreignId('user_id')->references($user->getKeyName())->on($user->getTable())->constrained()->cascadeOnDelete();
            $table->foreignId('filter_set_id')->references('id')->on('filament_filter_sets')->constrained()->cascadeOnDelete();
            $table->smallInteger('sort_order')->default(1);
        });
    }

    public function down(): void
    {
        Schema::drop('filament_filter_set_user');
    }
};
