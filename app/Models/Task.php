<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasAttachments;
    use HasFactory;

    /**
     * Task status'
     */
    public const TASK_ASSIGNED = 'assigned';
    public const TASK_COMPLETE = 'complete';
    public const TASK_EXPIRED = 'expired';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_tasks')
                    ->withPivot(['assigned_by_id', 'completed_at', 'assigned_at', 'expires_at'])
                    ->as('assignment')
                    ->using(TaskAssignment::class)
                    ->withTimestamps();
    }
}
