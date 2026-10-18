<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('hide_public_profile')->default(false)->after('cover_photo');
            $table->string('public_profile_key', 16)->nullable()->unique()->after('hide_public_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['public_profile_key']);
            $table->dropColumn(['hide_public_profile', 'public_profile_key']);
        });
    }
};
