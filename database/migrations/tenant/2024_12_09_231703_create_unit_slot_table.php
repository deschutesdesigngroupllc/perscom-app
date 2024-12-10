<?php

declare(strict_types=1);

use App\Models\Slot;
use App\Models\Unit;
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
        Schema::create('units_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Unit::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Slot::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_slots');
    }
};
