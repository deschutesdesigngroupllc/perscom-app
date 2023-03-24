<?php

namespace App\Nova;

use App\Notifications\Admin\NewSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Subscription extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Laravel\Cashier\Subscription::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'stripe_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'stripe_id', 'stripe_price', 'stripe_status'];

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Tenant: {$this->owner->name}";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Tenant', 'owner', Tenant::class)->sortable(),
            Text::make('Name')->rules(['required'])->placeholder('default')->sortable(),
            Text::make('Stripe ID')->readonly(function ($request) {
                return $request->isUpdateOrUpdateAttachedRequest();
            })->rules(['required']),
            Badge::make('Stripe Status')->map([
                'active' => 'success',
                'incomplete' => 'warning',
                'incomplete_expired' => 'danger',
                'trialing' => 'info',
                'past_due' => 'warning',
                'canceled' => 'danger',
                'unpaid' => 'danger',
            ])->sortable(),
            Text::make('Stripe Price')->readonly(function ($request) {
                return $request->isUpdateOrUpdateAttachedRequest();
            })->rules(['required']),
            Number::make('Quantity')->rules(['required'])->sortable(),
            DateTime::make('Trial Ends At')->sortable(),
            DateTime::make('Ends At')->sortable(),
            HasMany::make('Items', 'items', SubscriptionItem::class),
        ];
    }

    /**
     * @param  Request  $request
     * @return false
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * @param  Request  $request
     * @return false
     */
    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    /**
     * Register a callback to be called after the resource is created.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterCreate(NovaRequest $request, Model $model)
    {
        Notification::send(\App\Models\User::all(), new NewSubscription($model));
    }
}
