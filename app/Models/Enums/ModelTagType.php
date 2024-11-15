<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ModelTagType: string implements HasDescription, HasLabel
{
    case ASSIGNMENT_RECORD = 'assignment_record';
    case AWARD_RECORD = 'award_record';
    case COMBAT_RECORD = 'combat_record';
    case QUALIFICATION_RECORD = 'qualification_record';
    case RANK_RECORD = 'rank_record';
    case SERVICE_RECORD = 'service_record';
    case USER = 'user';

    public function getLabel(): ?string
    {
        return Str::of($this->value)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            ModelTagType::ASSIGNMENT_RECORD => 'For content that is attached to an assignment record.',
            ModelTagType::AWARD_RECORD => 'For content that is attached to an award record.',
            ModelTagType::COMBAT_RECORD => 'For content that is attached to a combat record.',
            ModelTagType::QUALIFICATION_RECORD => 'For content that is attached to a qualification record.',
            ModelTagType::RANK_RECORD => 'For content that is attached to a rank record.',
            ModelTagType::SERVICE_RECORD => 'For content that is attached to a service record.',
            ModelTagType::USER => 'For content that revolves around a user. This most often is either the recipient of some time of record (assignment/award etc.) or the currently logged in user viewing the content.'
        };
    }
}
