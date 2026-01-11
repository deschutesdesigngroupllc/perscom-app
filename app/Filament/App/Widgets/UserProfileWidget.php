<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EventResource;
use App\Filament\App\Resources\UserResource;
use App\Models\Event;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UserProfileWidget extends Widget
{
    use HasWidgetShield;

    protected static ?int $sort = -5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.user-profile-widget';

    protected ?User $user = null;

    protected ?Rank $rank = null;

    protected ?Position $position = null;

    protected ?Unit $unit = null;

    protected Collection $upcomingEvents;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->user = $user;

        $this->rank = $user->rank;
        $this->position = $user->position;
        $this->unit = $user->unit;

        $this->upcomingEvents = Event::query()
            ->where('starts', '>=', now())
            ->orderBy('starts')
            ->limit(3)
            ->get();
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function getRankImageUrl(): ?string
    {
        return $this->rank?->image?->image_url;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function getUnitImageUrl(): ?string
    {
        return $this->unit?->image?->image_url;
    }

    public function getUserUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->user]);
    }

    public function getRankRecordsUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->user, 'relation' => '4']);
    }

    public function getAssignmentRecordsUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->user, 'relation' => '0']);
    }

    public function getUpcomingEvents(): Collection
    {
        return $this->upcomingEvents;
    }

    public function getEventUrl(Event $event): string
    {
        return EventResource::getUrl('view', ['record' => $event]);
    }

    public function getEventsListUrl(): string
    {
        return EventResource::getUrl('index');
    }
}
