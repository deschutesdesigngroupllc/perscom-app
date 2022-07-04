<?php

namespace App\Providers;

use App\Listeners\TenantCouldNotBeIdentified;
use App\Models\Tenant;
use App\Observers\TenantObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
	    TenantCouldNotBeIdentifiedOnDomainException::class => [
	    	TenantCouldNotBeIdentified::class
	    ]
    ];


	/**
	 * The model observers for your application.
	 *
	 * @var array
	 */
	protected $observers = [
		Tenant::class => TenantObserver::class,
	];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
