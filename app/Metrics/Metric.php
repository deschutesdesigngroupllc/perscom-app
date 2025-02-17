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
    public static function increment(string $class, int $amount = 1): BaseMetric
    {
        return with(new $class, function (Metric $metric) use ($amount) {
            return BaseMetric::query()
                ->incrementOrCreate([
                    'created_at' => now()->startOfDay(),
                    'key' => $metric->key(),
                ], step: $amount);
        });
    }

    /**
     * @param  class-string  $class
     */
    public static function total(string $class, ?Closure $query = null): int
    {
        return with(new $class, function (Metric $metric) use ($query) {
            return (int) BaseMetric::query()
                ->where('key', $metric->key())
                ->when(filled($query), fn (Builder $builder) => value($query, $builder))
                ->sum('count');
        });
    }

    /**
     * @param  class-string  $class
     */
    public static function average(string $class, ?Closure $query = null): int
    {
        return with(new $class, function (Metric $metric) use ($query) {
            return (int) BaseMetric::query()
                ->where('key', $metric->key())
                ->when(filled($query), fn (Builder $builder) => value($query, $builder))
                ->when(blank($query), fn (Builder $builder) => $builder->whereBetween('created_at', [now()->startOfYear(), now()->startOfMonth()]))
                ->average('count');
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
        return BaseMetric::query()
            ->where('key', $this->key());
    }
}
