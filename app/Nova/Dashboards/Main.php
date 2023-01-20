<?php

namespace App\Nova\Dashboards;

use App\Models\Announcement;
use App\Models\Message;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UpdatesPerformed;
use App\Nova\Metrics\UsersOnline;
use Carbon\Carbon;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Perscom\AlertCard\AlertCard;
use Perscom\DashboardQuickActions\DashboardQuickActions;
use Perscom\DashboardTitle\DashboardTitle;

class Main extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return 'Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            (new DashboardTitle())->withTitle(setting('dashboard_title') ?? \tenant('name'))
                                  ->withSubtitle(setting('dashboard_subtitle')),
            $this->setupAlertCard(),
            new NewUsers(),
            new UsersOnline(),
            new UpdatesPerformed(),
            new DashboardQuickActions(),
        ];
    }

    /**
     * @return AlertCard
     */
    protected function setupAlertCard()
    {
        $card = new AlertCard();

        Announcement::query()
                    ->whereDate('expires_at', '>', now())
                    ->orWhereNull('expires_at')
                    ->each(function ($announcement) use ($card) {
                        $card->withAnnouncement($announcement->title, $announcement->content, $announcement->color);
                    });

        Message::query()->active()->ordered()->each(function ($message) use ($card) {
            $card->withSystemMessage($message->title, $message->message, $message->link_text, $message->url);
        });

        if (! Request::isDemoMode() && FeatureFlag::isOn('billing') && Auth::user()->hasPermissionTo('manage:billing')) {
            if (\tenant()->onTrial()) {
                $date = \tenant()->trial_ends_at;
                $ends = Carbon::parse($date)->toFormattedDateString();
                $left = Carbon::parse($date)->longRelativeToNowDiffForHumans();
                $card->withSubscriptionMessage("You are currently on trial. Your trial will end $left on $ends.", 'Sign Up For Subscription', route('spark.portal'));
            } elseif (! \tenant()->subscribed()) {
                $card->withSubscriptionMessage('You do not currently have an active subscription. Please sign up for a subscription to continue using PERSCOM.', 'Sign Up For Subscription', route('spark.portal'));
            }
        }

        if (Request::isDemoMode()) {
            $card->withSystemMessage('You are currently in demo mode. Not all features will be available.');
        }

        return $card;
    }
}
