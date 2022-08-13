<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Events\NullDispatcher;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Nova;
use Spark\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends \Stancl\Tenancy\Database\Models\Tenant implements TenantWithDatabase
{
    use Actionable;
    use Billable;
    use HasFactory;
    use HasDomains;
    use HasDatabase;

    /**
     * @var null
     */
    protected static $eventDispatcher = null;

    /**
     * Booted method
     */
    protected static function booted()
    {
        parent::booted();

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['domain', 'database_status'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['domains'];

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
            'card_brand',
            'card_last_four',
            'card_expiration',
            'extra_billing_information',
            'trial_ends_at',
            'billing_address',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_postal_code',
            'billing_country',
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
     * @return mixed|null
     */
    public function getDomainAttribute()
    {
        return $this->domains->first();
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
}
