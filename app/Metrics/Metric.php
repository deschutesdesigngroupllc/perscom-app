<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Models\Metric as BaseMetric;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class Metric
{
    /**
     * @param  class-string  $class
     */
    public static function increment(string $class, int $amount = 1): int
    {
        return with(new $class, function (Metric $metric) use ($amount) {
            return $metric
                ->query()
                ->whereDate('created_at', now()->startOfDay())
                ->increment('count', $amount);
        });
    }

    /**
     * @param  class-string  $class
     */
    public static function decrement(string $class, int $amount = 1): int
    {
        return with(new $class, function (Metric $metric) use ($amount) {
            return $metric
                ->query()
                ->whereDate('created_at', now()->startOfDay())
                ->decrement('count', $amount);
        });
    }

    /**
     * @param  class-string  $class
     */
    public static function total(string $class, ?Closure $query = null): int
    {
        return with(new $class, function (Metric $metric) use ($query) {
            return (int) $metric
                ->query()
                ->when(filled($query), fn (Builder $builder) => value($query, $builder))
                ->sum('count') ?? 0;
        });
    }

    /**
     * @param  class-string  $class
     */
    public static function average(string $class, ?Closure $query = null): int
    {
        return with(new $class, function (Metric $metric) use ($query) {
            return (int) $metric
                ->query()
                ->when(filled($query), fn (Builder $builder) => value($query, $builder))
                ->when(blank($query), fn (Builder $builder) => $builder->whereBetween('created_at', [now()->startOfYear(), now()->startOfMonth()]))
                ->average('count') ?? 0;
        });
    }

    public function key(): string
    {
        return Str::of(class_basename($this))
            ->snake()
            ->toString();
    }

    public function query(): Builder
    {
        $result = BaseMetric::query()
            ->where('key', $this->key());

        if (! $result->exists()) {
            $result->create([
                'key' => $this->key(),
                'count' => 0,
                'created_at' => now()->startOfDay(),
            ]);
        }

        return $result;
    }
}
