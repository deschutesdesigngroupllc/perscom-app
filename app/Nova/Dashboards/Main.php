<?php

namespace App\Nova\Dashboards;

use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Message;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UpcomingEvents;
use App\Nova\Metrics\UsersOnline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Perscom\AlertCard\AlertCard;
use Perscom\DashboardQuickActions\DashboardQuickActions;
use Perscom\DashboardTitle\DashboardTitle;
use Perscom\Newsfeed\Newsfeed;

class Main extends Dashboard
{
    public function name(): string
    {
        return 'Dashboard';
    }

    public function cards(): array
    {
        return [
            $this->setupDashboardTitle(),
            $this->setupAlertCard(),
            new NewUsers(),
            new UsersOnline(),
            new UpcomingEvents(),
            $this->setupNewsfeed(),
            $this->setupDashboardQuickActions(),
        ];
    }

    protected function setupDashboardTitle(): DashboardTitle
    {
        return tap(new DashboardTitle())
            ->withTitle(setting('dashboard_title') ?? \tenant('name'))
            ->withSubtitle(setting('dashboard_subtitle'));
    }

    protected function setupNewsfeed(): Newsfeed
    {
        $width = $this->canSeeAdminQuickActions() ? '2/3' : 'full';

        return tap(new Newsfeed())->width($width);
    }

    protected function setupAlertCard(): AlertCard
    {
        $card = new AlertCard();

        Announcement::query()
            ->whereDate('expires_at', '>', now())
            ->orWhereNull('expires_at')
            ->each(function ($announcement) use ($card) {
                $card->withAnnouncement($announcement->title, $announcement->content, $announcement->color);
            });

        Message::query()
            ->active()
            ->ordered()
            ->each(function ($message) use ($card) {
                $card->withSystemMessage($message->title, $message->message, $message->link_text, $message->url);
            });

        if (! Request::isDemoMode() && Gate::check('billing', Auth::user())) {
            if (\tenant()->onTrial()) {
                $date = \tenant()->trial_ends_at;
                $ends = Carbon::parse($date)
                    ->toFormattedDateString();
                $left = Carbon::parse($date)
                    ->longRelativeToNowDiffForHumans();
                $card->withSubscriptionMessage("You are currently on trial. Your trial will end $left on $ends.", 'Sign Up For Subscription', route('spark.portal'));
            } elseif (! \tenant()->subscribed()) {
                $card->withSubscriptionMessage('You do not currently have an active subscription. Please sign up for a subscription to continue using PERSCOM.', 'Sign Up For Subscription', route('spark.portal'));
            }
        }

        if (Request::isDemoMode()) {
            $card->withSystemMessage('Demo Mode', 'You are currently in demo mode. Not all features will be available.');
        }

        return $card;
    }

    protected function setupDashboardQuickActions(): DashboardQuickActions
    {
        return tap(new DashboardQuickActions())->canSee(function () {
            return $this->canSeeAdminQuickActions();
        });
    }

    protected function canSeeAdminQuickActions(): bool
    {
        return Gate::check('create', User::class) ||
            Gate::check('create', AssignmentRecord::class) ||
            Gate::check('create', ServiceRecord::class) ||
            Gate::check('create', CombatRecord::class) ||
            Gate::check('create', RankRecord::class) ||
            Gate::check('create', AwardRecord::class);
    }
}
