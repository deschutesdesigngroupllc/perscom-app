<?php

declare(strict_types=1);

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Event::query()->where('repeats', true)->get()->each(function (Event $event) {
            $event->schedule()->create(Arr::only($event->getAttributes(), [
                'frequency',
                'interval',
                'end_type',
                'count',
                'until',
                'by_day',
                'by_month_day',
                'by_month',
                'rrule',
            ]));
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'frequency',
                'interval',
                'end_type',
                'count',
                'until',
                'by_day',
                'by_month_day',
                'by_month',
                'rrule',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->after('repeats', function (Blueprint $table) {
                $table->text('frequency')->nullable();
                $table->integer('interval')->default(1);
                $table->string('end_type')->nullable();
                $table->integer('count')->nullable();
                $table->timestamp('until')->nullable();
                $table->json('by_day')->nullable();
                $table->json('by_month')->nullable();
                $table->json('by_set_position')->nullable();
                $table->json('by_month_day')->nullable();
                $table->json('by_year_day')->nullable();
                $table->string('rrule')->nullable();
            });
        });
    }
};
