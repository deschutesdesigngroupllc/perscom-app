<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Pages\PromotionBoard;
use App\Filament\App\Pages\Readiness;
use App\Filament\App\Resources\InviteResource;
use App\Filament\App\Resources\SubmissionResource;
use App\Filament\App\Resources\UserResource;
use App\Models\Invite;
use App\Models\Submission;
use App\Models\User;
use App\Services\PromotionEligibilityService;
use App\Services\ReadinessService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NeedsAttentionWidget extends Widget
{
    protected static ?int $sort = -20;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.needs-attention-widget';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && (
            $user->can('approve', User::class)
            || $user->can('viewAny', Submission::class)
            || $user->can('viewAny', Invite::class)
            || PromotionBoard::canAccess()
            || Readiness::canAccess()
        );
    }

    /**
     * Get the pending items that need the user's attention.
     *
     * @return array<int, array{key: string, count: int, label: string, description: string, url: string, icon: Heroicon, color: string}>
     */
    public function getItems(): array
    {
        return collect([
            Gate::check('approve', User::class) ? [
                'key' => 'approvals',
                'count' => User::query()->where('approved', false)->count(),
                'label' => 'Awaiting Approval',
                'description' => 'New members waiting for an administrator before they can sign in.',
                'url' => UserResource::getUrl('index'),
                'icon' => Heroicon::OutlinedUserPlus,
                'color' => 'warning',
            ] : null,
            Gate::check('viewAny', Submission::class) ? [
                'key' => 'submissions',
                'count' => Submission::query()->unread()->count(),
                'label' => 'Unread Submissions',
                'description' => 'Form responses nobody has opened yet.',
                'url' => SubmissionResource::getUrl('index'),
                'icon' => Heroicon::OutlinedInboxArrowDown,
                'color' => 'info',
            ] : null,
            PromotionBoard::canAccess() ? [
                'key' => 'promotions',
                'count' => PromotionEligibilityService::eligibleCount(),
                'label' => 'Eligible for Promotion',
                'description' => 'Members who meet every requirement for their next rank.',
                'url' => PromotionBoard::getUrl(),
                'icon' => Heroicon::OutlinedChevronDoubleUp,
                'color' => 'brass',
            ] : null,
            Readiness::canAccess() ? [
                'key' => 'expiring',
                'count' => ReadinessService::expiringItems()->count(),
                'label' => 'Expiring in 30 Days',
                'description' => 'Qualifications and credentials that need renewal soon.',
                'url' => Readiness::getUrl(),
                'icon' => Heroicon::OutlinedClock,
                'color' => 'warning',
            ] : null,
            Gate::check('viewAny', Invite::class) ? [
                'key' => 'invites',
                'count' => Invite::query()->pending()->count(),
                'label' => 'Pending Invitations',
                'description' => 'Invitations sent but not yet accepted.',
                'url' => InviteResource::getUrl('index'),
                'icon' => Heroicon::OutlinedEnvelope,
                'color' => 'gray',
            ] : null,
        ])
            ->filter(fn (?array $item): bool => $item !== null && $item['count'] > 0)
            ->values()
            ->all();
    }
}
