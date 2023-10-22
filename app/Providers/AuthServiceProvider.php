<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Attachment;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Banner;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\Element;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Form;
use App\Models\Group;
use App\Models\Image;
use App\Models\Like;
use App\Models\Mail;
use App\Models\Message;
use App\Models\NewsfeedItem;
use App\Models\PassportClient;
use App\Models\PassportClientLog;
use App\Models\PassportToken;
use App\Models\PassportTokenLog;
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
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Unit;
use App\Models\User;
use App\Models\Webhook;
use App\Policies\ActionPolicy;
use App\Policies\ActivityPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentRecordsPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AwardPolicy;
use App\Policies\AwardRecordsPolicy;
use App\Policies\BannerPolicy;
use App\Policies\CalendarPolicy;
use App\Policies\CombatRecordsPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ElementPolicy;
use App\Policies\EventPolicy;
use App\Policies\EventRegistrationPolicy;
use App\Policies\FormPolicy;
use App\Policies\GroupPolicy;
use App\Policies\ImagePolicy;
use App\Policies\LikePolicy;
use App\Policies\MailPolicy;
use App\Policies\MessagePolicy;
use App\Policies\NewsfeedItemPolicy;
use App\Policies\PassportClientLogPolicy;
use App\Policies\PassportClientPolicy;
use App\Policies\PassportTokenLogPolicy;
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
use App\Policies\TagPolicy;
use App\Policies\TaskAssignmentPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use App\Policies\WebhookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
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
        Activity::class => ActivityPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        AssignmentRecord::class => AssignmentRecordsPolicy::class,
        Attachment::class => AttachmentPolicy::class,
        Award::class => AwardPolicy::class,
        AwardRecord::class => AwardRecordsPolicy::class,
        Banner::class => BannerPolicy::class,
        Calendar::class => CalendarPolicy::class,
        CombatRecord::class => CombatRecordsPolicy::class,
        Document::class => DocumentPolicy::class,
        Element::class => ElementPolicy::class,
        Event::class => EventPolicy::class,
        EventRegistration::class => EventRegistrationPolicy::class,
        Form::class => FormPolicy::class,
        Group::class => GroupPolicy::class,
        Image::class => ImagePolicy::class,
        Like::class => LikePolicy::class,
        Mail::class => MailPolicy::class,
        Message::class => MessagePolicy::class,
        NewsfeedItem::class => NewsfeedItemPolicy::class,
        PassportClient::class => PassportClientPolicy::class,
        PassportClientLog::class => PassportClientLogPolicy::class,
        PassportToken::class => PassportTokenPolicy::class,
        PassportTokenLog::class => PassportTokenLogPolicy::class,
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
        Tag::class => TagPolicy::class,
        TaskAssignment::class => TaskAssignmentPolicy::class,
        Task::class => TaskPolicy::class,
        Unit::class => UnitPolicy::class,
        User::class => UserPolicy::class,
        Webhook::class => WebhookPolicy::class,
    ];

    public function boot(): void
    {
        Auth::viaRequest('api', static function () {
            return Auth::guard('passport')->user() ?? Auth::guard('jwt')->user();
        });
    }
}
