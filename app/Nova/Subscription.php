<?php

namespace App\Nova;

use App\Notifications\Admin\NewSubscription;
use App\Nova\Actions\OpenStripeSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
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
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Tenant', 'owner', Tenant::class)
                ->sortable(),
            Text::make('Name')
                ->rules(['required'])
                ->placeholder('default')
                ->hideFromIndex()
                ->sortable(),
            Text::make('ID', 'stripe_id')->readonly(function ($request) {
                return $request->isUpdateOrUpdateAttachedRequest();
            })->rules(['required']),
            Text::make('Price', 'stripe_price')->readonly(function ($request) {
                return $request->isUpdateOrUpdateAttachedRequest();
            })->rules(['required']),
            Text::make('Plan', function () {
                return $this->owner->sparkPlan()->name ?? null;
            }),
            Badge::make('Status', 'stripe_status')->map([
                'active' => 'success',
                'incomplete' => 'warning',
                'incomplete_expired' => 'danger',
                'trialing' => 'info',
                'past_due' => 'warning',
                'canceled' => 'danger',
                'unpaid' => 'danger',
            ])->label(function ($value) {
                return Str::replace('_', ' ', $value);
            })->sortable(),
            Number::make('Quantity')
                ->rules(['required'])->sortable(),
            DateTime::make('Trial Ends At')
                ->sortable(),
            DateTime::make('Ends At')
                ->sortable(),
            HasMany::make('Items', 'items', SubscriptionItem::class),
        ];
    }

    /**
     * @return false
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * @return false
     */
    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [new OpenStripeSubscription()];
    }

    /**
     * Register a callback to be called after the resource is created.
     *
     * @return void
     */
    public static function afterCreate(NovaRequest $request, Model $model)
    {
        Notification::send(\App\Models\User::all(), new NewSubscription($model));
    }
}
