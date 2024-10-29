<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum WebhookEvent: string implements HasLabel
{
    case ASSIGNMENT_RECORD_CREATED = 'assignmentrecord.created';
    case ASSIGNMENT_RECORD_UPDATED = 'assignmentrecord.updated';
    case ASSIGNMENT_RECORD_DELETED = 'assignmentrecord.deleted';
    case AWARD_RECORD_CREATED = 'awardrecord.created';
    case AWARD_RECORD_UPDATED = 'awardrecord.updated';
    case AWARD_RECORD_DELETED = 'awardrecord.deleted';
    case CALENDAR_CREATED = 'calendar.created';
    case CALENDAR_UPDATED = 'calendar.updated';
    case CALENDAR_DELETED = 'calendar.deleted';
    case COMBAT_RECORD_CREATED = 'combatrecord.created';
    case COMBAT_RECORD_UPDATED = 'combatrecord.updated';
    case COMBAT_RECORD_DELETED = 'combatrecord.deleted';
    case EVENT_CREATED = 'event.created';
    case EVENT_UPDATED = 'event.updated';
    case EVENT_DELETED = 'event.deleted';
    case MESSAGE_CREATED = 'message.created';
    case MESSAGE_UPDATED = 'message.updated';
    case MESSAGE_DELETED = 'message.deleted';
    case QUALIFICATION_RECORD_CREATED = 'qualificationrecord.created';
    case QUALIFICATION_RECORD_UPDATED = 'qualificationrecord.updated';
    case QUALIFICATION_RECORD_DELETED = 'qualificationrecord.deleted';
    case RANK_RECORD_CREATED = 'rankrecord.created';
    case RANK_RECORD_UPDATED = 'rankrecord.updated';
    case RANK_RECORD_DELETED = 'rankrecord.deleted';
    case SERVICE_RECORD_CREATED = 'servicerecord.created';
    case SERVICE_RECORD_UPDATED = 'servicerecord.updated';
    case SERVICE_RECORD_DELETED = 'servicerecord.deleted';
    case SUBMISSION_CREATED = 'submission.created';
    case SUBMISSION_UPDATED = 'submission.updated';
    case SUBMISSION_DELETED = 'submission.deleted';
    case TEST_WEBHOOK = 'webhook.test';
    case USER_CREATED = 'user.created';
    case USER_UPDATED = 'user.updated';
    case USER_DELETED = 'user.deleted';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
