<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $automation_id
 * @property string $subject_type
 * @property int $subject_id
 * @property \Illuminate\Support\Carbon $run_on
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Automation $automation
 * @property-read Model $subject
 *
 * @method static Builder<static>|AutomationRun forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|AutomationRun newModelQuery()
 * @method static Builder<static>|AutomationRun newQuery()
 * @method static Builder<static>|AutomationRun query()
 * @method static Builder<static>|AutomationRun whereAutomationId($value)
 * @method static Builder<static>|AutomationRun whereCreatedAt($value)
 * @method static Builder<static>|AutomationRun whereId($value)
 * @method static Builder<static>|AutomationRun whereRunOn($value)
 * @method static Builder<static>|AutomationRun whereSubjectId($value)
 * @method static Builder<static>|AutomationRun whereSubjectType($value)
 * @method static Builder<static>|AutomationRun whereUpdatedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class AutomationRun extends Model
{
    protected $fillable = [
        'automation_id',
        'subject_type',
        'subject_id',
        'run_on',
    ];

    /**
     * @return BelongsTo<Automation, $this>
     */
    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function forSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject::class)
            ->where('subject_id', $subject->getKey());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'run_on' => 'date',
        ];
    }
}
