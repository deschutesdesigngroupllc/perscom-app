<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\SubscriptionPlanType;
use App\Models\Enums\SubscriptionStatus;
use App\Observers\TenantObserver;
use App\Traits\ClearsResponseCache;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Optional;
use Illuminate\Support\Str;
use Laravel\Pennant\Concerns\HasFeatures;
use Laravel\Pennant\Contracts\FeatureScopeable;
use Spark\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\TenantCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $website
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $pm_expiration
 * @property string|null $extra_billing_information
 * @property Carbon|null $trial_ends_at
 * @property string|null $billing_address
 * @property string|null $billing_address_line_2
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_postal_code
 * @property string|null $vat_id
 * @property array $invoice_emails
 * @property string|null $billing_country
 * @property array<array-key, mixed>|null $data
 * @property Carbon|null $last_login_at
 * @property Carbon|null $setup_completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Domain|null $custom_domain
 * @property-read Optional|string|null|null $custom_url
 * @property-read string $database_status
 * @property-read Domain|null $domain
 * @property-read Collection<int, Domain> $domains
 * @property-read int|null $domains_count
 * @property-read Domain|null $fallback_domain
 * @property-read Optional|string|null|null $fallback_url
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read bool $setup_completed
 * @property-read Optional|string|null|null $slug
 * @property-read string|null $stripe_url
 * @property-read SubscriptionPlanType $subscription_plan
 * @property-read SubscriptionStatus $subscription_status
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Optional|string|null|null $url
 * @property string|null $tenancy_db_name
 *
 * @method static TenantCollection<int, static> all($columns = ['*'])
 * @method static TenantFactory factory($count = null, $state = [])
 * @method static TenantCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Tenant hasExpiredGenericTrial()
 * @method static Builder<static>|Tenant newModelQuery()
 * @method static Builder<static>|Tenant newQuery()
 * @method static Builder<static>|Tenant onGenericTrial()
 * @method static Builder<static>|Tenant query()
 * @method static Builder<static>|Tenant whereBillingAddress($value)
 * @method static Builder<static>|Tenant whereBillingAddressLine2($value)
 * @method static Builder<static>|Tenant whereBillingCity($value)
 * @method static Builder<static>|Tenant whereBillingCountry($value)
 * @method static Builder<static>|Tenant whereBillingPostalCode($value)
 * @method static Builder<static>|Tenant whereBillingState($value)
 * @method static Builder<static>|Tenant whereCreatedAt($value)
 * @method static Builder<static>|Tenant whereData($value)
 * @method static Builder<static>|Tenant whereEmail($value)
 * @method static Builder<static>|Tenant whereExtraBillingInformation($value)
 * @method static Builder<static>|Tenant whereId($value)
 * @method static Builder<static>|Tenant whereInvoiceEmails($value)
 * @method static Builder<static>|Tenant whereLastLoginAt($value)
 * @method static Builder<static>|Tenant whereName($value)
 * @method static Builder<static>|Tenant wherePmExpiration($value)
 * @method static Builder<static>|Tenant wherePmLastFour($value)
 * @method static Builder<static>|Tenant wherePmType($value)
 * @method static Builder<static>|Tenant whereSetupCompletedAt($value)
 * @method static Builder<static>|Tenant whereStripeId($value)
 * @method static Builder<static>|Tenant whereTrialEndsAt($value)
 * @method static Builder<static>|Tenant whereUpdatedAt($value)
 * @method static Builder<static>|Tenant whereVatId($value)
 * @method static Builder<static>|Tenant whereWebsite($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(TenantObserver::class)]
class Tenant extends BaseTenant implements FeatureScopeable, TenantWithDatabase
{
    use Billable;
    use CentralConnection;
    use ClearsResponseCache;
    use HasDatabase;
    use HasDomains;
    use HasFactory;
    use HasFeatures;
    use Notifiable;

    protected $appends = [
        'database_status',
        'url',
        'custom_url',
        'fallback_url',
        'setup_completed',
        'subscription_status',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'website',
            'stripe_id',
            'pm_type',
            'pm_last_four',
            'pm_expiration',
            'extra_billing_information',
            'trial_ends_at',
            'billing_address',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_postal_code',
            'billing_country',
            'vat_id',
            'invoice_emails',
            'last_login_at',
            'setup_completed_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return Attribute<string, never>
     */
    public function databaseStatus(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getAttribute('tenancy_db_name') ? 'created' : 'creating'
        )->shouldCache();
    }

    /**
     * @return Attribute<?Domain, never>
     */
    public function customDomain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->domains->where('is_custom_subdomain', '=', true)
                ->sortBy('created_at', SORT_REGULAR, true)
                ->first()
        )->shouldCache();
    }

    /**
     * @return Attribute<?Domain, never>
     */
    public function fallbackDomain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->domains->where('is_custom_subdomain', '=', false)
                ->sortBy('created_at', SORT_REGULAR, true)
                ->first()
        )->shouldCache();
    }

    /**
     * @return Attribute<?Domain, never>
     */
    public function domain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->custom_domain ?? $this->fallback_domain
        )->shouldCache();
    }

    /**
     * @return Attribute<Optional|string|null, never>
     */
    public function customUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->custom_domain)->url
        )->shouldCache();
    }

    /**
     * @return Attribute<Optional|string|null, never>
     */
    public function fallbackUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->fallback_domain)->url
        )->shouldCache();
    }

    /**
     * @return Attribute<bool, never>
     */
    public function setupCompleted(): Attribute
    {
        return Attribute::get(fn (): bool => ! is_null($this->setup_completed_at))
            ->shouldCache();
    }

    /**
     * @return Attribute<Optional|string|null, never>
     */
    public function slug(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->domain)->domain
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function stripeUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (blank($this->stripe_id)) {
                return null;
            }

            return match (true) {
                App::isProduction() => "https://dashboard.stripe.com/customers/$this->stripe_id",
                default => "https://dashboard.stripe.com/test/customers/$this->stripe_id"
            };
        });
    }

    /**
     * @return Attribute<Optional|string|null, never>
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => $this->custom_url ?? $this->fallback_url
        )->shouldCache();
    }

    /**
     * @return Attribute<SubscriptionPlanType, never>
     */
    public function subscriptionPlan(): Attribute
    {
        return Attribute::get(function (): SubscriptionPlanType {
            $plan = $this->sparkPlan();

            if (blank($plan)) {
                return SubscriptionPlanType::NONE;
            }

            return SubscriptionPlanType::from(Str::lower($plan->name));

        })->shouldCache();
    }

    /**
     * @return Attribute<SubscriptionStatus, never>
     */
    public function subscriptionStatus(): Attribute
    {
        return Attribute::get(function (): SubscriptionStatus {
            $subscription = $this->subscription();

            if (blank($subscription)) {
                return SubscriptionStatus::None;
            }

            return SubscriptionStatus::from($subscription->stripe_status);

        })->shouldCache();
    }

    public function stripeName(): ?string
    {
        return $this->name;
    }

    public function routeNotificationForMail(Notification $notification): string
    {
        return $this->email;
    }

    public function toFeatureIdentifier(string $driver): string
    {
        return (string) $this->getTenantKey();
    }

    public function route(string $name, array $parameters = []): string
    {
        return "$this->url".route($name, array_merge($parameters, [
            'tenant' => $this,
        ]), false);
    }

    public function resolveRouteBinding($value, $field = null): Tenant|Model|null
    {
        /** @var Domain $domain */
        $domain = Domain::query()->where('domain', $value)->first();

        if (filled($domain)) {
            return $domain->tenant;
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'setup_complete' => 'boolean',
            'trial_ends_at' => 'datetime',
            'setup_completed_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }
}
