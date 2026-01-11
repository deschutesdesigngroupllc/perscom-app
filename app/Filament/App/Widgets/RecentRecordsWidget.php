<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\UserResource;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecentRecordsWidget extends Widget
{
    use HasWidgetShield;

    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.recent-records-widget';

    protected ?User $user = null;

    protected ?RankRecord $latestRankRecord = null;

    protected ?AssignmentRecord $latestAssignmentRecord = null;

    protected ?AwardRecord $latestAwardRecord = null;

    protected ?QualificationRecord $latestQualificationRecord = null;

    protected ?CombatRecord $latestCombatRecord = null;

    protected ?ServiceRecord $latestServiceRecord = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->user = $user;

        /** @var RankRecord|null $rankRecord */
        $rankRecord = $user->rank_records()->with('rank.image')->latest()->first();
        $this->latestRankRecord = $rankRecord;

        /** @var AssignmentRecord|null $assignmentRecord */
        $assignmentRecord = $user->assignment_records()->with(['position', 'unit.image', 'status'])->latest()->first();
        $this->latestAssignmentRecord = $assignmentRecord;

        /** @var AwardRecord|null $awardRecord */
        $awardRecord = $user->award_records()->with('award.image')->latest()->first();
        $this->latestAwardRecord = $awardRecord;

        /** @var QualificationRecord|null $qualificationRecord */
        $qualificationRecord = $user->qualification_records()->with('qualification.image')->latest()->first();
        $this->latestQualificationRecord = $qualificationRecord;

        /** @var CombatRecord|null $combatRecord */
        $combatRecord = $user->combat_records()->latest()->first();
        $this->latestCombatRecord = $combatRecord;

        /** @var ServiceRecord|null $serviceRecord */
        $serviceRecord = $user->service_records()->latest()->first();
        $this->latestServiceRecord = $serviceRecord;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserRecordsUrl(string $relation): string
    {
        return UserResource::getUrl('view', ['record' => $this->user, 'relation' => $relation]);
    }

    public function getLatestRankRecord(): ?RankRecord
    {
        return $this->latestRankRecord;
    }

    public function getLatestAssignmentRecord(): ?AssignmentRecord
    {
        return $this->latestAssignmentRecord;
    }

    public function getLatestAwardRecord(): ?AwardRecord
    {
        return $this->latestAwardRecord;
    }

    public function getLatestQualificationRecord(): ?QualificationRecord
    {
        return $this->latestQualificationRecord;
    }

    public function getLatestCombatRecord(): ?CombatRecord
    {
        return $this->latestCombatRecord;
    }

    public function getLatestServiceRecord(): ?ServiceRecord
    {
        return $this->latestServiceRecord;
    }
}
