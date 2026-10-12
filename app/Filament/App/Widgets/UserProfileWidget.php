<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Events\OccurrenceData;
use App\Data\Promotions\PromotionEvaluationData;
use App\Filament\App\Resources\EventResource;
use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Filament\App\Resources\UserResource\RelationManagers\AssignmentRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\AwardRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\RankRecordsRelationManager;
use App\Models\AwardRecord;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Rank;
use App\Models\User;
use App\Services\EventAttendanceService;
use App\Services\EventOccurrenceService;
use App\Services\PromotionEligibilityService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\CarbonInterval;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;

/**
 * The signed-in member's service record: rank, assignment, upcoming events, awards and progress
 * toward their next promotion.
 */
class UserProfileWidget extends Widget
{
    use HasWidgetShield;

    protected static ?int $sort = -5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.user-profile-widget';

    /**
     * @var array<int, class-string>|null
     */
    protected ?array $relationManagers = null;

    protected ?PromotionEvaluationData $promotion = null;

    public function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function timeInService(): ?string
    {
        return $this->humanize($this->user()->time_in_service);
    }

    public function timeInGrade(): ?string
    {
        return $this->humanize($this->user()->time_in_grade);
    }

    public function timeInAssignment(): ?string
    {
        return $this->humanize($this->user()->time_in_assignment);
    }

    public function promotion(): PromotionEvaluationData
    {
        return $this->promotion ??= PromotionEligibilityService::evaluate($this->user());
    }

    public function hasPromotionRequirements(): bool
    {
        return $this->promotion()->nextRank instanceof Rank && filled($this->promotion()->requirements);
    }

    /**
     * @return EloquentCollection<int, AwardRecord>
     */
    public function awardRecords(): EloquentCollection
    {
        return AwardRecord::query()
            ->where('user_id', $this->user()->getKey())
            ->whereHas('award')
            ->with(['award.image'])
            ->get();
    }

    /**
     * Upcoming and in-progress events with the member's registration status and check-in availability.
     *
     * @return array<int, array{event: Event, status: ?EventRegistrationStatus, canCheckIn: bool, checkInUrl: ?string}>
     */
    public function upcomingEvents(): array
    {
        $user = $this->user();

        return Event::query()
            ->where(fn ($query) => $query->where('ends', '>=', now())->orWhere('repeats', true))
            ->orderBy('starts')
            ->limit(4)
            ->with(['image', 'schedule'])
            ->get()
            ->map(function (Event $event) use ($user): array {
                $occurrence = EventOccurrenceService::nearest($event);
                $status = $occurrence instanceof OccurrenceData ? EventAttendanceService::statusFor($occurrence, $user) : null;

                $canCheckIn = $status !== EventRegistrationStatus::ATTENDED
                    && ($status instanceof EventRegistrationStatus || $event->registration_enabled)
                    && EventAttendanceService::isWithinCheckInWindow($event);

                return [
                    'event' => $event,
                    'status' => $status,
                    'canCheckIn' => $canCheckIn,
                    'checkInUrl' => $canCheckIn ? EventAttendanceService::checkInUrl($event) : null,
                ];
            })
            ->values()
            ->all();
    }

    public function showsAwards(): bool
    {
        return $this->relationIndex(AwardRecordsRelationManager::class) !== null;
    }

    public function getUserUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->user()]);
    }

    public function getRankRecordsUrl(): ?string
    {
        return $this->relationUrl(RankRecordsRelationManager::class);
    }

    public function getAssignmentRecordsUrl(): ?string
    {
        return $this->relationUrl(AssignmentRecordsRelationManager::class);
    }

    public function getAwardRecordsUrl(): ?string
    {
        return $this->relationUrl(AwardRecordsRelationManager::class);
    }

    public function getEventUrl(Event $event): string
    {
        return EventResource::getUrl('view', ['record' => $event]);
    }

    public function getEventsListUrl(): string
    {
        return EventResource::getUrl('index');
    }

    /**
     * Profile record tabs depend on which record types the organization hides, so link to a tab by
     * looking up its position rather than assuming one.
     *
     * @param  class-string  $relationManager
     */
    protected function relationUrl(string $relationManager): ?string
    {
        $index = $this->relationIndex($relationManager);

        return $index === null ? null : UserResource::getUrl('view', ['record' => $this->user(), 'relation' => (string) $index]);
    }

    /**
     * @param  class-string  $relationManager
     */
    protected function relationIndex(string $relationManager): ?int
    {
        $this->relationManagers ??= new ViewUser()->getRelationManagers();

        $index = array_search($relationManager, $this->relationManagers, true);

        return $index === false ? null : (int) $index;
    }

    protected function humanize(?CarbonInterval $interval): ?string
    {
        return $interval?->forHumans(['parts' => 2]);
    }
}
