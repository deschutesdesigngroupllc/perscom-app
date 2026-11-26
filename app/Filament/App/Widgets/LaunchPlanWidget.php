<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Clusters\Integrations\Pages\ConnectedServices;
use App\Filament\App\Clusters\Settings\Pages\Dashboard as DashboardSettingsPage;
use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Resources\InviteResource;
use App\Filament\App\Resources\RankResource;
use App\Filament\App\Resources\UserResource;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\Invite;
use App\Models\Position;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\DashboardSettings;
use App\Settings\IntegrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

use function in_array;
use function sprintf;

class LaunchPlanWidget extends Widget
{
    public const string DISMISSED_KEY = 'launch_plan';

    /**
     * Founder plus this many people makes a roster worth managing.
     */
    public const int MEMBER_TARGET = 5;

    protected static ?int $sort = -30;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.launch-plan-widget';

    public static function canView(): bool
    {
        /** @var ?User $user */
        $user = Auth::user();

        return $user instanceof User
            && $user->hasRole(Utils::getSuperAdminName())
            && ! static::isDismissed()
            && collect((new self)->getSteps())->contains(fn (array $step): bool => ! $step['done']);
    }

    public static function isDismissed(): bool
    {
        return in_array(static::DISMISSED_KEY, (array) UserSettingsService::get('dismissed_widgets', []), true);
    }

    public function dismiss(): void
    {
        $dismissed = (array) UserSettingsService::get('dismissed_widgets', []);

        UserSettingsService::save('dismissed_widgets', array_values(array_unique([...$dismissed, static::DISMISSED_KEY])));

        $this->redirect(Dashboard::getUrl(), navigate: true);
    }

    /**
     * Get the launch plan steps and whether each one is done.
     *
     * @return array<int, array{key: string, label: string, description: string, done: bool, action: string, url: string}>
     */
    public function getSteps(): array
    {
        /** @var ?User $user */
        $user = Auth::user();

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        $integrations = resolve(IntegrationSettings::class);

        return [
            [
                'key' => 'structure',
                'label' => 'Build your structure',
                'description' => 'Ranks, units and positions are the backbone of your roster.',
                'done' => Rank::query()->exists() && Unit::query()->exists() && Position::query()->exists(),
                'action' => 'Open Ranks',
                'url' => RankResource::getUrl('index'),
            ],
            [
                'key' => 'profile',
                'label' => 'Complete your profile',
                'description' => 'Add your real name, rank and unit so you appear on the roster.',
                'done' => $user instanceof User && $user->name !== 'Admin' && filled($user->rank_id) && filled($user->unit_id),
                'action' => 'Edit Your Profile',
                'url' => $user instanceof User ? UserResource::getUrl('edit', ['record' => $user]) : UserResource::getUrl('index'),
            ],
            [
                'key' => 'members',
                'label' => sprintf('Add your first %d members', static::MEMBER_TARGET),
                'description' => 'Invite people or add them yourself. Each one gets a profile and a service history.',
                'done' => User::query()->count() + Invite::query()->pending()->count() > static::MEMBER_TARGET,
                'action' => 'Invite Members',
                'url' => InviteResource::getUrl('index'),
            ],
            [
                'key' => 'record',
                'label' => 'Record a promotion, award or assignment',
                'description' => "Records build each member's history and drive time in grade and readiness.",
                'done' => RankRecord::query()->exists() || AwardRecord::query()->exists() || AssignmentRecord::query()->exists(),
                'action' => 'Open Members',
                'url' => UserResource::getUrl('index'),
            ],
            [
                'key' => 'url',
                'label' => 'Choose your PERSCOM URL',
                'description' => 'Pick a memorable address your members can find again.',
                'done' => filled($tenant?->custom_url) || filled(resolve(DashboardSettings::class)->subdomain),
                'action' => 'Set Your URL',
                'url' => DashboardSettingsPage::getUrl(['tab' => 'domain::data::tab']),
            ],
            [
                'key' => 'integrations',
                'label' => 'Connect Discord or Slack',
                'description' => 'Post promotions, awards and event reminders where your members already talk.',
                'done' => filled(data_get($integrations->discord_settings, 'discord_server')) || filled(data_get($integrations->slack_settings, 'team_id')),
                'action' => 'Open Integrations',
                'url' => ConnectedServices::getUrl(),
            ],
        ];
    }
}
