<?php

namespace App\Nova\Metrics;

use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class UsersOnline extends Value
{
    /**
     * @var string
     */
    public $icon = 'user';

    /**
     * @var string
     */
    public $helpText = 'Current online users is checked every two minutes.';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $keys = \App\Models\User::all()->map(function ($user) {
            return "user.online.$user->id";
        })->toArray();

        $count = collect(\Illuminate\Support\Facades\Cache::tags('user.online')->many($keys))
            ->filter(function ($value) {
                return $value === true;
            })->count();

        return new ValueResult($count);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(2);
    }

    /**
     * @return string
     */
    public function name()
    {
        return 'Current '.Str::plural(Str::title(setting('localization_users', 'Users'))).' Online';
    }
}
