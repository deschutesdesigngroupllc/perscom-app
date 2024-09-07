<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantObserver;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Optional;
use Laravel\Pennant\Concerns\HasFeatures;
use Laravel\Pennant\Contracts\FeatureScopeable;
use Spark\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

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
 * @property-read Domain|null $custom_domain
 * @property-read Optional|string|null|null $custom_url
 * @property-read mixed $database_status
 * @property-read Domain|null $domain
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read Domain|null $fallback_domain
 * @property-read Optional|string|null|null $fallback_url
 * @property-read array $invoice_emails
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Optional|string|null|null $slug
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Optional|string|null|null $url
 * @property string|null $tenancy_db_name
 *
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static Builder|Tenant hasExpiredGenericTrial()
 * @method static Builder|Tenant newModelQuery()
 * @method static Builder|Tenant newQuery()
 * @method static Builder|Tenant onGenericTrial()
 * @method static Builder|Tenant onlyTrashed()
 * @method static Builder|Tenant query()
 * @method static Builder|Tenant whereBillingAddress($value)
 * @method static Builder|Tenant whereBillingAddressLine2($value)
 * @method static Builder|Tenant whereBillingCity($value)
 * @method static Builder|Tenant whereBillingCountry($value)
 * @method static Builder|Tenant whereBillingPostalCode($value)
 * @method static Builder|Tenant whereBillingState($value)
 * @method static Builder|Tenant whereCreatedAt($value)
 * @method static Builder|Tenant whereData($value)
 * @method static Builder|Tenant whereDeletedAt($value)
 * @method static Builder|Tenant whereEmail($value)
 * @method static Builder|Tenant whereExtraBillingInformation($value)
 * @method static Builder|Tenant whereId($value)
 * @method static Builder|Tenant whereLastLoginAt($value)
 * @method static Builder|Tenant whereName($value)
 * @method static Builder|Tenant wherePmExpiration($value)
 * @method static Builder|Tenant wherePmLastFour($value)
 * @method static Builder|Tenant wherePmType($value)
 * @method static Builder|Tenant whereReceiptEmails($value)
 * @method static Builder|Tenant whereStripeId($value)
 * @method static Builder|Tenant whereTrialEndsAt($value)
 * @method static Builder|Tenant whereUpdatedAt($value)
 * @method static Builder|Tenant whereVatId($value)
 * @method static Builder|Tenant whereWebsite($value)
 * @method static Builder|Tenant withTrashed()
 * @method static Builder|Tenant withoutTrashed()
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
    use SoftDeletes;

    protected $appends = [
        'database_status',
        'url',
        'custom_url',
        'fallback_url',
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
            'last_login_at',
            'vat_id',
            'receipt_emails',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    public function databaseStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getAttribute('tenancy_db_name') ? 'created' : 'creating'
        )->shouldCache();
    }

    public function customDomain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->domains->where('is_custom_subdomain', '=', true)
                ->sortBy('created_at', SORT_REGULAR, true)
                ->first()
        )->shouldCache();
    }

    public function fallbackDomain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->domains->where('is_custom_subdomain', '=', false)
                ->sortBy('created_at', SORT_REGULAR, true)
                ->first()
        )->shouldCache();
    }

    public function domain(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Domain => $this->custom_domain ?? $this->fallback_domain
        )->shouldCache();
    }

    public function customUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->custom_domain)->url
        )->shouldCache();
    }

    public function fallbackUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->fallback_domain)->url
        )->shouldCache();
    }

    public function slug(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => optional($this->domain)->domain
        )->shouldCache();
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|string|null => $this->custom_url ?? $this->fallback_url
        )->shouldCache();
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
        if ($tenant = self::query()->whereHas('domains', fn (Builder $query) => $query->where('domain', $value))->first()) {
            return $tenant;
        }

        return parent::resolveRouteBinding($value, $field);
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }
}
