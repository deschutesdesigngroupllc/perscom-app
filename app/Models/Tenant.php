<?php

namespace App\Models;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Events\NullDispatcher;
use Illuminate\Notifications\Notifiable;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Nova;
use Laravel\Pennant\Concerns\HasFeatures;
use Laravel\Pennant\Contracts\FeatureScopeable;
use Spark\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

/**
 * App\Models\Tenant
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $website
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $pm_expiration
 * @property string|null $extra_billing_information
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property string|null $billing_address
 * @property string|null $billing_address_line_2
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_postal_code
 * @property string|null $vat_id
 * @property string|null $receipt_emails
 * @property string|null $billing_country
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read mixed|null $custom_domain
 * @property-read mixed|null $custom_url
 * @property-read string $database_status
 * @property-read mixed|null $domain
 * @property-read mixed|null $fallback_domain
 * @property-read mixed|null $fallback_url
 * @property-read array $invoice_emails
 * @property-read mixed|null $url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feature> $pennants
 * @property-read int|null $pennants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Cashier\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property string|null $tenancy_db_name
 *
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereExtraBillingInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePmExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereReceiptEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereVatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Tenant extends \Stancl\Tenancy\Database\Models\Tenant implements FeatureScopeable, TenantWithDatabase
{
    use Actionable;
    use Billable;
    use CentralConnection;
    use HasDatabase;
    use HasDomains;
    use HasFactory;
    use HasFeatures;
    use Notifiable;
    use SoftDeletes;

    protected static ?Dispatcher $eventDispatcher = null;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = ['database_status', 'url', 'custom_url', 'fallback_url'];

    /**
     * @var string[]
     */
    protected $with = ['domains'];

    protected static function booted(): void
    {
        static::creating(function ($tenant) {
            Nova::whenServing(function () use ($tenant) {
                self::$eventDispatcher = $tenant::getEventDispatcher();
                $tenant::unsetEventDispatcher();
            });
        });

        static::created(function ($tenant) {
            Nova::whenServing(function () use ($tenant) {
                if ($dispatcher = self::$eventDispatcher) {
                    $tenant::setEventDispatcher(new NullDispatcher($dispatcher));
                }
            });
        });
    }

    /**
     * @return string[]
     */
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
            'last_login_at',
            'vat_id',
            'receipt_emails',
            'created_at',
            'updated_at',
        ];
    }

    public function getDatabaseStatusAttribute(): string
    {
        return $this->getAttribute('tenancy_db_name') ? 'created' : 'creating';
    }

    public function getCustomDomainAttribute(): mixed
    {
        return $this->domains->where('is_custom_subdomain', '=', true)
            ->sortBy('created_at', SORT_REGULAR, true)
            ->first();
    }

    public function getFallbackDomainAttribute(): mixed
    {
        return $this->domains->where('is_custom_subdomain', '=', false)
            ->sortBy('created_at', SORT_REGULAR, true)
            ->first();
    }

    public function getDomainAttribute(): mixed
    {
        return $this->custom_domain ?? $this->fallback_domain;
    }

    public function getCustomUrlAttribute(): mixed
    {
        return optional($this->custom_domain)->url;
    }

    public function getFallbackUrlAttribute(): mixed
    {
        return optional($this->fallback_domain)->url;
    }

    public function getUrlAttribute(): mixed
    {
        return optional($this->domain)->url;
    }

    public function stripeName(): ?string
    {
        return $this->name;
    }

    public function routeNotificationForMail(\Illuminate\Notifications\Notification $notification): string
    {
        return $this->email;
    }

    public function toFeatureIdentifier(string $driver): string
    {
        return (string) $this->getTenantKey();
    }

    public function pennants(): HasMany
    {
        return $this->hasMany(Feature::class, 'scope');
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function route(string $name, array $parameters = []): string
    {
        return "$this->url".route($name, $parameters, false);
    }
}
