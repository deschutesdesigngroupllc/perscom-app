<?php

namespace App\Providers;

use App\Models\Award;
use App\Models\Document;
use App\Models\Forms\Form;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Records\Award as AwardRecords;
use App\Models\Records\Assignment as AssignmentRecords;
use App\Models\Records\Combat as CombatRecords;
use App\Models\Records\Qualification as QualificationRecords;
use App\Models\Records\Rank as RankRecords;
use App\Models\Records\Service as ServiceRecords;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Policies\ActionEventPolicy;
use App\Policies\AssignmentRecordsPolicy;
use App\Policies\AwardPolicy;
use App\Policies\AwardRecordsPolicy;
use App\Policies\CombatRecordsPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\FormPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PositionPolicy;
use App\Policies\QualificationPolicy;
use App\Policies\QualificationRecordsPolicy;
use App\Policies\RankPolicy;
use App\Policies\RankRecordsPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServiceRecordsPolicy;
use App\Policies\SpecialtyPolicy;
use App\Policies\StatusPolicy;
use App\Policies\SubscriptionItemPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Laravel\Nova\Actions\ActionEvent;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ActionEvent::class => ActionEventPolicy::class,
        Award::class => AwardPolicy::class,
        AwardRecords::class => AwardRecordsPolicy::class,
        AssignmentRecords::class => AssignmentRecordsPolicy::class,
        CombatRecords::class => CombatRecordsPolicy::class,
        Document::class => DocumentPolicy::class,
        Form::class => FormPolicy::class,
        Permission::class => PermissionPolicy::class,
        Position::class => PositionPolicy::class,
        Qualification::class => QualificationPolicy::class,
        QualificationRecords::class => QualificationRecordsPolicy::class,
        Rank::class => RankPolicy::class,
        RankRecords::class => RankRecordsPolicy::class,
        Role::class => RolePolicy::class,
        ServiceRecords::class => ServiceRecordsPolicy::class,
        Specialty::class => SpecialtyPolicy::class,
        Status::class => StatusPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        SubscriptionItem::class => SubscriptionItemPolicy::class,
        Unit::class => UnitPolicy::class,
        User::class => UserPolicy::class,
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
