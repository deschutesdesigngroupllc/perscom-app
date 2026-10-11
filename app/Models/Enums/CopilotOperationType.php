<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\Message;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\User;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CopilotOperationType: string implements HasColor, HasLabel
{
    case RANK_RECORD = 'rank_record';
    case ASSIGNMENT_RECORD = 'assignment_record';
    case AWARD_RECORD = 'award_record';
    case QUALIFICATION_RECORD = 'qualification_record';
    case STATUS_RECORD = 'status_record';
    case MESSAGE = 'message';

    public function getLabel(): string
    {
        return match ($this) {
            self::RANK_RECORD => 'Rank Record',
            self::ASSIGNMENT_RECORD => 'Assignment Record',
            self::AWARD_RECORD => 'Award Record',
            self::QUALIFICATION_RECORD => 'Qualification Record',
            self::STATUS_RECORD => 'Status Change',
            self::MESSAGE => 'Message',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::RANK_RECORD => 'warning',
            self::ASSIGNMENT_RECORD => 'info',
            self::AWARD_RECORD => 'success',
            self::QUALIFICATION_RECORD => 'primary',
            self::STATUS_RECORD => 'gray',
            self::MESSAGE => 'danger',
        };
    }

    /**
     * The model the actor must be allowed to create for this operation. Status changes
     * update the member, so they are authorized against the user instead.
     *
     * @return class-string
     */
    public function getAuthorizableModel(): string
    {
        return match ($this) {
            self::RANK_RECORD => RankRecord::class,
            self::ASSIGNMENT_RECORD => AssignmentRecord::class,
            self::AWARD_RECORD => AwardRecord::class,
            self::QUALIFICATION_RECORD => QualificationRecord::class,
            self::STATUS_RECORD => User::class,
            self::MESSAGE => Message::class,
        };
    }

    public function getAuthorizationAbility(): string
    {
        return match ($this) {
            self::STATUS_RECORD => 'update',
            default => 'create',
        };
    }
}
