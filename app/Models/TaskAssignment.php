<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\TaskAssignmentStatus;
use App\Observers\TaskAssignmentObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Database\Factories\TaskAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
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
 * @property-read User|null $assigned_by
 * @property-read bool $complete
 * @property-read bool $expired
 * @property-read bool $past_due
 * @property-read TaskAssignmentStatus $status
 * @property-read Task|null $task
 * @property-read User $user
 *
 * @method static Builder<static>|TaskAssignment assigned()
 * @method static Builder<static>|TaskAssignment expired()
 * @method static TaskAssignmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|TaskAssignment newModelQuery()
 * @method static Builder<static>|TaskAssignment newQuery()
 * @method static Builder<static>|TaskAssignment pastDue()
 * @method static Builder<static>|TaskAssignment query()
 * @method static Builder<static>|TaskAssignment user(User $user)
 * @method static Builder<static>|TaskAssignment whereAssignedAt($value)
 * @method static Builder<static>|TaskAssignment whereAssignedById($value)
 * @method static Builder<static>|TaskAssignment whereCompletedAt($value)
 * @method static Builder<static>|TaskAssignment whereCreatedAt($value)
 * @method static Builder<static>|TaskAssignment whereDueAt($value)
 * @method static Builder<static>|TaskAssignment whereExpiresAt($value)
 * @method static Builder<static>|TaskAssignment whereId($value)
 * @method static Builder<static>|TaskAssignment whereTaskId($value)
 * @method static Builder<static>|TaskAssignment whereUpdatedAt($value)
 * @method static Builder<static>|TaskAssignment whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(TaskAssignmentObserver::class)]
class TaskAssignment extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasUser;

    protected $table = 'users_tasks';

    protected $fillable = [
        'task_id',
        'assigned_by_id',
        'assigned_at',
        'due_at',
        'completed_at',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'complete',
        'expired',
        'past_due',
        'status',
    ];

    public function scopeAssigned(Builder $query): void
    {
        $query->whereNull('completed_at')->where(function (Builder $query): void {
            $query->whereNull('expires_at')->orWhere(function (Builder $query): void {
                $query->whereNotNull('expires_at')->where('expires_at', '>', now());
            });
        })->where(function (Builder $query): void {
            $query->whereNull('due_at')->orWhere(function (Builder $query): void {
                $query->whereNotNull('due_at')->where('due_at', '>', now());
            });
        });
    }

    public function scopeExpired(Builder $query): void
    {
        $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now())->where(function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query->whereNotNull('completed_at')->whereDate('completed_at', '>', now());
            })->orWhereNull('completed_at');
        });
    }

    public function scopePastDue(Builder $query): void
    {
        $query->whereNotNull('due_at')->where(function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query->whereNull('completed_at')->whereDate('due_at', '<', now());
            })->orWhere(function (Builder $query): void {
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function assigned_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<Task, $this>
     */
    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TaskAssignment $assignment): void {
            $assignment->assigned_by_id ??= Auth::user()?->getAuthIdentifier() ?? null;
            $assignment->assigned_at ??= now();
        });
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
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
    }
}
