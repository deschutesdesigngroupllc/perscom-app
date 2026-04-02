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

        Schema::create('filament_filter_sets_managed_preset_views', function (Blueprint $table) {
            $userClass = Archilex\AdvancedTables\Support\Config::getUser();
            $user = new $userClass;

            $table->id();

            /** @phpstan-ignore method.notFound */
            $table->foreignId('user_id')->references($user->getKeyName())->on($user->getTable())->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('resource');
            $table->boolean('is_favorite')->default(true);
            $table->smallInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('filament_filter_sets_managed_preset_views');
    }
};
