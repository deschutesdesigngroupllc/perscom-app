<?php

namespace App\Models;

use App\Models\Scopes\TaskAssignmentScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskAssignment extends Pivot
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'users_tasks';

    /**
     * @var string[]
     */
    protected $appends = ['status', 'expired'];

    /**
     * @var string[]
     */
    protected $casts = [
        'completed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
        'expires_at' => 'datetime',
        'expired' => 'boolean',
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
     * @return string
     */
    public function getStatusAttribute()
    {
        if ($this->expired) {
            return Task::TASK_EXPIRED;
        }

        return $this->completed_at ? Task::TASK_COMPLETE : Task::TASK_ASSIGNED;
    }

    /**
     * @return bool
     */
    public function getExpiredAttribute()
    {
        return $this->expires_at && Carbon::parse($this->expires_at)->isPast();
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
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function attachments()
    {
        return $this->hasManyThrough(Attachment::class, Task::class);
    }
}
