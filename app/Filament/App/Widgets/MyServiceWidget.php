<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Promotions\PromotionEvaluationData;
use App\Models\AwardRecord;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\EventAttendanceService;
use App\Services\PromotionEligibilityService;
use Carbon\CarbonInterval;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;

class MyServiceWidget extends Widget
{
    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.my-service-widget';

    public static function canView(): bool
    {
        return Auth::user() instanceof User;
    }

    public function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function timeInService(): ?string
    {
        $joined = $this->user()->created_at;

        return $joined ? CarbonInterval::instance($joined->diff(now()))->forHumans(['parts' => 2]) : null;
    }

    public function timeInGrade(): ?string
    {
        $interval = $this->user()->time_in_grade;

        return $interval instanceof CarbonInterval ? $interval->forHumans(['parts' => 2]) : null;
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
            ->limit(5)
            ->with(['schedule', 'registrations' => fn ($query) => $query->whereKey($user->getKey())])
            ->get()
            ->map(function (Event $event): array {
                $registration = $event->registrations->first()?->getRelation('registration');
                $status = $registration?->status instanceof EventRegistrationStatus ? $registration->status : null;

                $canCheckIn = $status !== EventRegistrationStatus::ATTENDED
                    && ($status !== null || $event->registration_enabled)
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

    public function promotion(): PromotionEvaluationData
    {
        return PromotionEligibilityService::evaluate($this->user());
    }
}
