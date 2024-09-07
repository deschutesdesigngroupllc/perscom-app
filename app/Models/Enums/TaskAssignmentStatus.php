<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum TaskAssignmentStatus: string
{
    case TASK_ASSIGNED = 'assigned';
    case TASK_COMPLETE = 'complete';
    case TASK_COMPLETE_EXPIRED = 'complete_expired';
    case TASK_COMPLETE_PAST_DUE = 'complete_past_due';
    case TASK_EXPIRED = 'expired';
    case TASK_PASTDUE = 'past_due';
}
