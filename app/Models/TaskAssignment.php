<?php

namespace App\Models;

use App\Models\Enums\TaskAssignmentStatus;
use App\Models\Scopes\TaskAssignmentScope;
use App\Traits\HasUser;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\Models\TaskAssignment
 *
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property int|null $assigned_by_id
 * @property \Illuminate\Support\Carbon $assigned_at
 * @property \Illuminate\Support\Carbon|null $due_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assigned_by
 * @property-read bool $complete
 * @property-read bool $expired
 * @property-read bool $past_due
 * @property-read TaskAssignmentStatus $status
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read int|null $attachments_count
 *
 * @method static Builder|TaskAssignment assigned()
 * @method static Builder|TaskAssignment expired()
 * @method static \Database\Factories\TaskAssignmentFactory factory($count = null, $state = [])
 * @method static Builder|TaskAssignment newModelQuery()
 * @method static Builder|TaskAssignment newQuery()
 * @method static Builder|TaskAssignment pastDue()
 * @method static Builder|TaskAssignment query()
 * @method static Builder|TaskAssignment user(\App\Models\User $user)
 * @method static Builder|TaskAssignment whereAssignedAt($value)
 * @method static Builder|TaskAssignment whereAssignedById($value)
 * @method static Builder|TaskAssignment whereCompletedAt($value)
 * @method static Builder|TaskAssignment whereCreatedAt($value)
 * @method static Builder|TaskAssignment whereDueAt($value)
 * @method static Builder|TaskAssignment whereExpiresAt($value)
 * @method static Builder|TaskAssignment whereId($value)
 * @method static Builder|TaskAssignment whereTaskId($value)
 * @method static Builder|TaskAssignment whereUpdatedAt($value)
 * @method static Builder|TaskAssignment whereUserId($value)
 *
 * @mixin \Eloquent
 */
class TaskAssignment extends Pivot
{
    use HasFactory;
    use HasRelationships;
    use HasUser;

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
    protected $with = ['task', 'user'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
        'expires_at' => 'datetime',
        'expired' => 'boolean',
        'past_due' => 'boolean',
        'complete' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TaskAssignmentScope());
    }

    public function scopeAssigned(Builder $query): void
    {
        $query->whereNull('completed_at')->where(function (Builder $query) {
            $query->whereNull('expires_at')->orWhere(function (Builder $query) {
                $query->whereNotNull('expires_at')->where('expires_at', '>', now());
            });
        })->where(function (Builder $query) {
            $query->whereNull('due_at')->orWhere(function (Builder $query) {
                $query->whereNotNull('due_at')->where('due_at', '>', now());
            });
        });
    }

    public function scopeExpired(Builder $query): void
    {
        $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now())->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNotNull('completed_at')->whereDate('completed_at', '>', now());
            })->orWhereNull('completed_at');
        });
    }

    public function scopePastDue(Builder $query): void
    {
        $query->whereNotNull('due_at')->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNull('completed_at')->whereDate('due_at', '<', now());
            })->orWhere(function (Builder $query) {
                $query->whereNotNull('completed_at')->where('due_at', '<', DB::raw('completed_at'));
            });
        });
    }

    public function getStatusAttribute(): TaskAssignmentStatus
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

    public function complete(?CarbonInterface $completedAt = null): bool
    {
        return $this->update([
            'completed_at' => now(),
        ]);
    }

    public function getCompleteAttribute(): bool
    {
        return (bool) $this->completed_at;
    }

    public function getExpiredAttribute(): bool
    {
        return $this->expires_at &&
               Carbon::parse($this->expires_at)->isPast() &&
               (($this->complete && Carbon::parse($this->completed_at)->isAfter($this->expires_at)) ||
                ! $this->complete);
    }

    public function getPastDueAttribute(): bool
    {
        return $this->due_at &&
               ((! $this->complete && Carbon::parse($this->due_at)->isPast()) ||
                ($this->complete && Carbon::parse($this->due_at)->isBefore($this->completed_at)));
    }

    public function assigned_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    public function attachments(): HasManyDeep
    {
        return $this->hasManyDeep(Attachment::class, [Task::class], [null, ['model_type', 'model_id']]);
    }
}
