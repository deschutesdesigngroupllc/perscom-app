<?php

namespace App\Providers;

use App\Listeners\TenancyInitializedListener;
use App\Listeners\UserLoggedInListener;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Domain;
use App\Models\Mail;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Settings;
use App\Models\TaskAssignment;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\AssignmentRecordObserver;
use App\Observers\AwardRecordObserver;
use App\Observers\CombatRecordObserver;
use App\Observers\DomainObserver;
use App\Observers\FeatureObserver;
use App\Observers\MailObserver;
use App\Observers\QualificationRecordObserver;
use App\Observers\RankRecordObserver;
use App\Observers\ServiceRecordObserver;
use App\Observers\SettingsObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\TaskAssignmentObserver;
use App\Observers\TenantObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Cashier\Subscription;
use Stancl\Tenancy\Events\TenancyInitialized;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            UserLoggedInListener::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TenancyInitialized::class => [
            TenancyInitializedListener::class,
        ],
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
        AssignmentRecord::observe(AssignmentRecordObserver::class);
        AwardRecord::observe(AwardRecordObserver::class);
        CombatRecord::observe(CombatRecordObserver::class);
        Domain::observe(DomainObserver::class);
        Mail::observe(MailObserver::class);
        QualificationRecord::observe(QualificationRecordObserver::class);
        RankRecord::observe(RankRecordObserver::class);
        ServiceRecord::observe(ServiceRecordObserver::class);
        Settings::observe(SettingsObserver::class);
        Subscription::observe(SubscriptionObserver::class);
        TaskAssignment::observe(TaskAssignmentObserver::class);
        Tenant::observe(TenantObserver::class);
        User::observe(UserObserver::class);
    }
}
