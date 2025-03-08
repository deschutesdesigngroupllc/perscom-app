<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Activity;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

use function activity;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasLogs
{
    use LogsActivity;

    /**
     * @return MorphMany<Activity, TModel>
     */
    public function logs(): MorphMany
    {
        /** @var TModel $this */
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logFillable();
    }

    public function generateCreatedNewsfeedItem(ShouldGenerateNewsfeedItems $model): ?ActivityContract
    {
        $resource = Str::camelToLower(class_basename($model));

        $properties = [
            'headline' => $model->headlineForNewsfeedItem(),
            'text' => $model->textForNewsfeedItem(),
        ];

        if (filled($item = $model->itemForNewsfeedItem())) {
            $properties['item'] = $item;
        }

        $activity = activity('newsfeed')
            ->withProperties($properties);

        if (($recipient = $model->recipientForNewsfeedItem()) instanceof \App\Models\User) {
            $activity = $activity->performedOn($recipient);
        }

        $starter = Str::startsWith($resource, ['a', 'e', 'i', 'o', 'u'])
            ? 'An'
            : 'A';

        return $activity->log("$starter $resource has been created.");
    }

    protected static function bootHasLogs(): void
    {
        static::created(function ($model): void {
            if ($model instanceof ShouldGenerateNewsfeedItems) {
                $model->generateCreatedNewsfeedItem($model);
            }
        });
    }
}
