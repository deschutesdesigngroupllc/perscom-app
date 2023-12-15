<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Actions\ActionResource;

class Action extends ActionResource
{
    /**
     * @var class-string<TActionModel>
     */
    public static $model = \App\Models\Action::class;

    /**
     * Determine if this resource is available for navigation.
     *
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return true;
    }

    public static function label(): string
    {
        return 'Logs';
    }

    public static function uriKey(): string
    {
        return 'logs';
    }
}
