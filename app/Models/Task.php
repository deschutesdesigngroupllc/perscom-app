<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Task
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\Form|null $form
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 *
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasAttachments;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'instructions', 'form_id'];

    public static function boot(): void
    {
        parent::boot();

        static::deleted(function (Task $task) {
            $task->users()->detach();
        });
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_tasks')
            ->withPivot(['id', 'assigned_by_id', 'completed_at', 'assigned_at', 'expires_at'])
            ->as('assignment')
            ->using(TaskAssignment::class)
            ->withTimestamps();
    }
}
