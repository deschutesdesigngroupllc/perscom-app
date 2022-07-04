<?php

namespace App\Providers;

use App\Models\Records\Award as AwardRecords;
use App\Models\Records\Assignment as AssignmentRecords;
use App\Models\Records\Combat as CombatRecords;
use App\Models\Records\Qualification as QualificationRecords;
use App\Models\Records\Rank as RankRecords;
use App\Models\Records\Service as ServiceRecords;
use App\Policies\AssignmentRecordsPolicy;
use App\Policies\AwardRecordsPolicy;
use App\Policies\CombatRecordsPolicy;
use App\Policies\QualificationRecordsPolicy;
use App\Policies\RankRecordsPolicy;
use App\Policies\ServiceRecordsPolicy;
use App\Policies\SubscriptionItemPolicy;
use App\Policies\SubscriptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    	AwardRecords::class => AwardRecordsPolicy::class,
	    AssignmentRecords::class => AssignmentRecordsPolicy::class,
	    CombatRecords::class => CombatRecordsPolicy::class,
	    QualificationRecords::class => QualificationRecordsPolicy::class,
	    RankRecords::class => RankRecordsPolicy::class,
	    ServiceRecords::class => ServiceRecordsPolicy::class,
    	Subscription::class => SubscriptionPolicy::class,
	    SubscriptionItem::class => SubscriptionItemPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
