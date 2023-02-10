<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Attachment;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\Form;
use App\Models\Image;
use App\Models\Mail;
use App\Models\Message;
use App\Models\PassportClient;
use App\Models\PassportLog;
use App\Models\PassportToken;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\Role;
use App\Models\ServiceRecord;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\StatusRecord;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Unit;
use App\Models\User;
use App\Policies\ActionPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentRecordsPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AwardPolicy;
use App\Policies\AwardRecordsPolicy;
use App\Policies\CombatRecordsPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\FormPolicy;
use App\Policies\ImagePolicy;
use App\Policies\MailPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PassportClientPolicy;
use App\Policies\PassportLogPolicy;
use App\Policies\PassportTokenPolicy;
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
use App\Policies\StatusRecordPolicy;
use App\Policies\SubscriptionItemPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TaskAssignmentPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        Action::class => ActionPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        AssignmentRecord::class => AssignmentRecordsPolicy::class,
        Attachment::class => AttachmentPolicy::class,
        Award::class => AwardPolicy::class,
        AwardRecord::class => AwardRecordsPolicy::class,
        CombatRecord::class => CombatRecordsPolicy::class,
        Document::class => DocumentPolicy::class,
        Form::class => FormPolicy::class,
        Image::class => ImagePolicy::class,
        Mail::class => MailPolicy::class,
        Message::class => MessagePolicy::class,
        PassportClient::class => PassportClientPolicy::class,
        PassportLog::class => PassportLogPolicy::class,
        PassportToken::class => PassportTokenPolicy::class,
        Permission::class => PermissionPolicy::class,
        Position::class => PositionPolicy::class,
        Qualification::class => QualificationPolicy::class,
        QualificationRecord::class => QualificationRecordsPolicy::class,
        Rank::class => RankPolicy::class,
        RankRecord::class => RankRecordsPolicy::class,
        Role::class => RolePolicy::class,
        ServiceRecord::class => ServiceRecordsPolicy::class,
        Specialty::class => SpecialtyPolicy::class,
        Status::class => StatusPolicy::class,
        StatusRecord::class => StatusRecordPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        SubscriptionItem::class => SubscriptionItemPolicy::class,
        TaskAssignment::class => TaskAssignmentPolicy::class,
        Task::class => TaskPolicy::class,
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

        // Scope JWTs to the tenant they belong to
        Gate::before(function ($user) {
            if ($jwtUser = Auth::guard('jwt')->user()) {
                $payload = Auth::guard('jwt')->payload();
                if ($payload->get('tenant') !== tenant()->getTenantKey()) {
                    abort(401, 'You are not authorized to access this account.');
                }
            }
        });

        Auth::viaRequest('api', function (Request $request) {
            return Auth::guard('passport')->user() ?? Auth::guard('jwt')->user();
        });
    }
}
