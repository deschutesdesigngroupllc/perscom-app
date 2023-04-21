<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Events\NullDispatcher;
use Illuminate\Notifications\Notifiable;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Nova;
use Laravel\Pennant\Concerns\HasFeatures;
use Laravel\Pennant\Contracts\FeatureScopeable;
use Spark\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
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
 * @property array $receipt_emails
 * @property string|null $billing_country
 * @property string|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $data
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read mixed $custom_domain
 * @property-read mixed|null $custom_url
 * @property-read string $database_status
 * @property-read mixed|null $domain
 * @property-read mixed $fallback_domain
 * @property-read mixed|null $fallback_url
 * @property-read mixed|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spark\Receipt> $localReceipts
 * @property-read int|null $local_receipts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Cashier\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 *
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereBillingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereData($value)
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
 *
 * @mixin \Eloquent
 */
class Tenant extends \Stancl\Tenancy\Database\Models\Tenant implements TenantWithDatabase, FeatureScopeable
{
    use Actionable;
    use Billable;
    use HasFactory;
    use HasFeatures;
    use HasDomains;
    use HasDatabase;
    use Notifiable;

    /**
     * @var null
     */
    protected static $eventDispatcher = null;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['database_status', 'url', 'custom_url', 'fallback_url'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['domains'];

    /**
     * Booted method
     */
    protected static function booted()
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

    /**
     * @return string
     */
    public function getDatabaseStatusAttribute()
    {
        return $this->tenancy_db_name ? 'created' : 'creating';
    }

    /**
     * @return mixed
     */
    public function getCustomDomainAttribute()
    {
        return $this->domains->where('is_custom_subdomain', '=', true)
            ->sortBy('created_at', SORT_REGULAR, true)
            ->first();
    }

    /**
     * @return mixed
     */
    public function getFallbackDomainAttribute()
    {
        return $this->domains->where('is_custom_subdomain', '=', false)
            ->sortBy('created_at', SORT_REGULAR, true)
            ->first();
    }

    /**
     * @return mixed|null
     */
    public function getDomainAttribute()
    {
        return $this->custom_domain ?? $this->fallback_domain;
    }

    /**
     * @return mixed|null
     */
    public function getCustomUrlAttribute()
    {
        return optional($this->custom_domain)->url;
    }

    /**
     * @return mixed|null
     */
    public function getFallbackUrlAttribute()
    {
        return optional($this->fallback_domain)->url;
    }

    /**
     * @return mixed|null
     */
    public function getUrlAttribute()
    {
        return optional($this->domain)->url;
    }

    /**
     * Get the customer name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeName()
    {
        return $this->name;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function toFeatureIdentifier(string $driver): mixed
    {
        return (string) $this->getTenantKey();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pennants()
    {
        return $this->hasMany(Feature::class, 'scope');
    }
}
