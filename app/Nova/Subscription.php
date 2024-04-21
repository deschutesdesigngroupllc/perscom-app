<?php

namespace App\Nova;

use App\Nova\Actions\OpenStripeSubscription;
use Illuminate\Http\Request;
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
    public static string $model = \Laravel\Cashier\Subscription::class;

    /**
     * @var string
     */
    public static $title = 'stripe_id';

    /**
     * @var array
     */
    public static $search = ['id', 'stripe_id', 'stripe_price', 'stripe_status'];

    public function subtitle(): ?string
    {
        return "Tenant: {$this->owner->name}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Tenant', 'owner', Tenant::class)
                ->sortable(),
            Text::make('Type')
                ->rules(['required'])
                ->placeholder('default')
                ->hideFromIndex()
                ->sortable(),
            Text::make('ID', 'stripe_id')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            Text::make('Price', 'stripe_price')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            Text::make('Plan', function () {
                return optional($this->owner)->sparkPlan()->name ?? null;
            }),
            Badge::make('Status', 'stripe_status')
                ->map([
                    'active' => 'success',
                    'incomplete' => 'warning',
                    'incomplete_expired' => 'danger',
                    'trialing' => 'info',
                    'past_due' => 'warning',
                    'canceled' => 'danger',
                    'unpaid' => 'danger',
                ])
                ->label(function ($value) {
                    return Str::replace('_', ' ', $value);
                })
                ->sortable(),
            Number::make('Quantity')
                ->rules(['required'])
                ->sortable(),
            DateTime::make('Trial Ends At')
                ->sortable(),
            DateTime::make('Ends At')
                ->sortable(),
            HasMany::make('Items', 'items', SubscriptionItem::class),
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [new OpenStripeSubscription()];
    }
}
