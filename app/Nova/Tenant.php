<?php

namespace App\Nova;

use App\Models\Domain;
use App\Models\Tenant as TenantModel;
use App\Nova\Actions\CreateTenantDatabase;
use App\Nova\Actions\CreateTenantUser;
use App\Nova\Actions\DeleteTenantDatabase;
use App\Nova\Actions\ImpersonateTenant;
use App\Nova\Actions\ResetTenantDatabaseFactory;
use App\Nova\Actions\ResetTenantTrialDate;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Stancl\Tenancy\Events\DomainCreated;
use Stancl\Tenancy\Events\TenantCreated;

class Tenant extends Resource
{
    use HasActionsInTabs;
    use HasTabs;

    public static string $model = TenantModel::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name', 'email'];

    public function subtitle(): ?string
    {
        return "URL: {$this->url}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make('PERSCOM ID', 'id')
                ->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules(['required', Rule::unique('tenants', 'name')
                    ->ignore($this->id)])
                ->copyable(),
            Email::make('Email')
                ->sortable()
                ->hideFromIndex()
                ->rules(['required', Rule::unique('tenants', 'email')
                    ->ignore($this->id)]),
            Text::make('Website')
                ->hideFromIndex(),
            URL::make('Domain', 'url')
                ->displayUsing(function ($url) {
                    return $url;
                })
                ->exceptOnForms(),
            Boolean::make('Demo Account', function () {
                return (string) $this->id === (string) env('TENANT_DEMO_ID');
            })
                ->hideFromIndex()
                ->readonly(),
            Text::make('Domain', 'domain')
                ->rules([
                    'required',
                    'string',
                    'max:255',
                    Rule::unique(Domain::class, 'domain'),
                ])
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->fillUsing(function ($request) {
                    return null;
                }),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Last Login At')
                ->sortable()
                ->exceptOnForms(),
            DateTime::make('Created At')
                ->sortable()
                ->exceptOnForms()
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->sortable()
                ->exceptOnForms()
                ->onlyOnDetail(),
            Tabs::make('Relations', [
                Tab::make('Billing Settings', [
                    Text::make('Billing Address')
                        ->hideFromIndex(),
                    Text::make('Billing Address Line 2')
                        ->hideFromIndex(),
                    Text::make('Billing City')
                        ->hideFromIndex(),
                    Text::make('Billing State')
                        ->hideFromIndex(),
                    Text::make('Billing Postal Code')
                        ->hideFromIndex(),
                    Country::make('Billing Country')
                        ->hideFromIndex(),
                    Text::make('VAT ID')
                        ->hideFromIndex(),
                    Textarea::make('Extra Billing Information')
                        ->hideFromIndex(),
                ]),
                Tab::make('Current Subscription', [
                    Boolean::make('Customer', function ($model) {
                        return $model->hasStripeId();
                    }),
                    Boolean::make('On Trial', function ($model) {
                        return $model->onGenericTrial();
                    }),
                    Text::make('Plan', function () {
                        return $this->sparkPlan()->name ?? null;
                    }),
                    Badge::make('Status', function () {
                        return $this->subscription()->stripe_status ?? null;
                    })
                        ->map([
                            'active' => 'success',
                            'incomplete' => 'warning',
                            'incomplete_expired' => 'danger',
                            'trialing' => 'info',
                            'past_due' => 'warning',
                            'canceled' => 'danger',
                            'unpaid' => 'danger',
                            '' => 'info',
                        ])
                        ->label(function ($value) {
                            return $value ? Str::replace('_', ' ', $value) : 'No Subscription';
                        })
                        ->sortable(),
                    Text::make('Stripe ID')
                        ->hideFromIndex()
                        ->copyable(),
                    Text::make('Card Brand', 'pm_type')
                        ->hideFromIndex(),
                    Text::make('Card Last Four', 'pm_last_four')
                        ->hideFromIndex(),
                    Text::make('Card Expiration', 'pm_expiration')
                        ->hideFromIndex(),
                    DateTime::make('Trial Ends At')
                        ->hideFromIndex(),
                    DateTime::make('Plan Ends At')
                        ->hideFromIndex(),
                    Text::make('Receipt Emails')
                        ->hideFromIndex(),
                ]),
                Tab::make('Database', [
                    Text::make('Database Name', 'tenancy_db_name')
                        ->hideFromIndex(),
                    Status::make('Database Status')
                        ->hideFromIndex()
                        ->loadingWhen(['creating'])
                        ->failedWhen([])
                        ->readonly(),
                ]),
                Tab::make('Domains', [HasMany::make('Domains')]),
                Tab::make('Features', [
                    HasMany::make('Features', 'pennants', Feature::class),
                ]),
                Tab::make('Receipts', [HasMany::make('Receipts', 'localReceipts', Receipt::class)]),
                Tab::make('Subscriptions', [HasMany::make('Subscriptions')]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
        ];
    }

    public static function afterCreate(NovaRequest $request, Model $model): void
    {
        $values = $request->all();

        $domain = Domain::withoutEvents(function () use ($model, $values) {
            if ($model instanceof TenantModel) {
                return $model->domains()
                    ->create([
                        'domain' => $values['domain'],
                    ]);
            }
        });

        $model->load('domains');

        Event::dispatch(new TenantCreated($model));
        event('eloquent.created: '.TenantModel::class, $model);
        Event::dispatch(new DomainCreated($domain));
        event('eloquent.created: '.Domain::class, $domain);
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
        return [
            new CreateTenantUser(),
            new CreateTenantDatabase(),
            new ResetTenantTrialDate(),
            (new ImpersonateTenant())->onlyOnDetail(),
            new DeleteTenantDatabase(),
            new ResetTenantDatabaseFactory(),
        ];
    }
}
