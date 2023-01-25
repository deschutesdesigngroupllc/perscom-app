<?php

namespace App\Models;

use App\Models\Enums\TaskAssignmentStatus;
use App\Models\Scopes\TaskAssignmentScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class TaskAssignment extends Pivot
{
    use HasFactory;
    use HasRelationships;

    /**
     * @var string
     */
    protected $table = 'users_tasks';

    /**
     * @var string[]
     */
    protected $appends = ['complete', 'expired', 'past_due', 'status'];

    /**
     * @var string[]
     */
    protected $casts = [
        'completed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
        'expires_at' => 'datetime',
        'expired' => 'boolean',
        'past_due' => 'boolean',
        'complete' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new TaskAssignmentScope);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAssigned(Builder $query)
    {
        return $query->whereNull('completed_at')->where(function (Builder $query) {
            return $query->whereNull('expires_at')->orWhere(function (Builder $query) {
                $query->whereNotNull('expires_at')->where('expires_at', '>', now());
            });
        })->where(function (Builder $query) {
            return $query->whereNull('due_at')->orWhere(function (Builder $query) {
                $query->whereNotNull('due_at')->where('due_at', '>', now());
            });
        });
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeExpired(Builder $query)
    {
        return $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now())->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNotNull('completed_at')->whereDate('completed_at', '>', now());
            })->orWhereNull('completed_at');
        });
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePastDue(Builder $query)
    {
        return $query->whereNotNull('due_at')->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNull('completed_at')->whereDate('due_at', '<', now());
            })->orWhere(function (Builder $query) {
                $query->whereNotNull('completed_at')->where('due_at', '<', DB::raw('completed_at'));
            });
        });
    }

    /**
     * @return TaskAssignmentStatus
     */
    public function getStatusAttribute()
    {
        if ($this->expired) {
            if ($this->complete) {
                return TaskAssignmentStatus::TASK_COMPLETE_EXPIRED;
            }

            return TaskAssignmentStatus::TASK_EXPIRED;
        }

        if ($this->past_due) {
            if ($this->complete) {
                return TaskAssignmentStatus::TASK_COMPLETE_PAST_DUE;
            }

            return TaskAssignmentStatus::TASK_PASTDUE;
        }

        return $this->complete ? TaskAssignmentStatus::TASK_COMPLETE : TaskAssignmentStatus::TASK_ASSIGNED;
    }

    /**
     * @param  null  $completedAt
     * @return bool
     */
    public function complete($completedAt = null)
    {
        return $this->update([
            'completed_at' => now(),
        ]);
    }

    /**
     * @return bool
     */
    public function getCompleteAttribute()
    {
        return (bool) $this->completed_at;
    }

    /**
     * @return bool
     */
    public function getExpiredAttribute()
    {
        return $this->expires_at &&
               Carbon::parse($this->expires_at)->isPast() &&
               (($this->complete && Carbon::parse($this->completed_at)->isAfter($this->expires_at)) ||
                ! $this->complete);
    }

    /**
     * @return bool
     */
    public function getPastDueAttribute()
    {
        return $this->due_at &&
               ((! $this->complete && Carbon::parse($this->due_at)->isPast()) ||
                ($this->complete && Carbon::parse($this->due_at)->isBefore($this->completed_at)));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assigned_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function attachments()
    {
        return $this->hasManyDeep(Attachment::class, [Task::class], [null, ['model_type', 'model_id']]);
    }
}
