<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spark\Billable;
use Spatie\Url\Url;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends \Stancl\Tenancy\Database\Models\Tenant implements TenantWithDatabase
{
    use Billable;
    use HasFactory;
    use HasDomains;
    use HasDatabase;

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
	protected $appends = ['domain', 'url'];

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
	 * @return mixed|null
	 */
    public function getDomainAttribute()
    {
    	return optional($this->domains()->first())->domain;
    }

	/**
	 * @return mixed|null
	 */
	public function getUrlAttribute()
	{
		return optional($this->domain, function () {
			return Url::fromString($this->domain)
				->withScheme(app()->environment() === 'production' ? 'https' : 'http')
				->__toString();
		});
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
