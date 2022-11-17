<?php

namespace App\Providers;

use App\Listeners\TenantCouldNotBeIdentified;
use App\Models\Records\Assignment;
use App\Models\Records\Award;
use App\Models\Records\Combat;
use App\Models\Records\Qualification;
use App\Models\Records\Rank;
use App\Models\Records\Service;
use App\Models\Settings;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\Records\AssignmentRecordObserver;
use App\Observers\Records\AwardRecordObserver;
use App\Observers\Records\CombatRecordObserver;
use App\Observers\Records\QualificationRecordObserver;
use App\Observers\Records\RankRecordObserver;
use App\Observers\Records\ServiceRecordObserver;
use App\Observers\SettingsObserver;
use App\Observers\TenantObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
        TenantCouldNotBeIdentifiedOnDomainException::class => [TenantCouldNotBeIdentified::class],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Assignment::observe(AssignmentRecordObserver::class);
        Award::observe(AwardRecordObserver::class);
        Combat::observe(CombatRecordObserver::class);
        Qualification::observe(QualificationRecordObserver::class);
        Rank::observe(RankRecordObserver::class);
        Service::observe(ServiceRecordObserver::class);
        Settings::observe(SettingsObserver::class);
        Tenant::observe(TenantObserver::class);
        User::observe(UserObserver::class);
    }
}
