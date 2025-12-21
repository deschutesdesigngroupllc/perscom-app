<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\Tenant\SendModelNotifications;
use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * @mixin Eloquent
 */
trait HasModelNotifications
{
    public static function bootHasModelNotifications(): void
    {
        static::created(function ($model): void {
            if (filled($model)) {
                $event = Str::of($model::class)
                    ->classBasename()
                    ->singular()
                    ->lower()
                    ->append('.created')
                    ->toString();

                SendModelNotifications::dispatch($model, $event);
            }
        });

        static::updated(function ($model): void {
            if (filled($model)) {
                $event = Str::of($model::class)
                    ->classBasename()
                    ->singular()
                    ->lower()
                    ->append('.updated')
                    ->toString();

                SendModelNotifications::dispatch($model, $event);
            }
        });

        static::deleting(function ($model): void {
            if (filled($model)) {
                $event = Str::of($model::class)
                    ->classBasename()
                    ->singular()
                    ->lower()
                    ->append('.deleted')
                    ->toString();

                SendModelNotifications::dispatch($model, $event);
            }
        });

        static::deleting(fn ($model) => $model->modelNotifications()->delete());
    }

    public function modelNotifications(): MorphMany
    {
        /** @phpstan-ignore return.type */
        return $this->morphMany(ModelNotification::class, 'model');
    }
}
