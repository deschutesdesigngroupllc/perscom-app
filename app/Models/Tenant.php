<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spark\Billable;
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
			'receipt_emails'
		];
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
