<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rank_id
 * @property int|null $min_days_in_grade
 * @property int|null $min_days_in_service
 * @property int|null $min_events_attended_90d
 * @property array<array-key, mixed>|null $required_qualification_ids
 * @property array<array-key, mixed>|null $required_credential_ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Rank $rank
 *
 * @method static \Database\Factories\RankRequirementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereMinDaysInGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereMinDaysInService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereMinEventsAttended90d($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereRequiredCredentialIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereRequiredQualificationIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRequirement whereUpdatedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class RankRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank_id',
        'min_days_in_grade',
        'min_days_in_service',
        'min_events_attended_90d',
        'required_qualification_ids',
        'required_credential_ids',
    ];

    /**
     * @return BelongsTo<Rank, $this>
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function hasRequirements(): bool
    {
        return filled($this->min_days_in_grade)
            || filled($this->min_days_in_service)
            || filled($this->min_events_attended_90d)
            || filled($this->required_qualification_ids)
            || filled($this->required_credential_ids);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_days_in_grade' => 'integer',
            'min_days_in_service' => 'integer',
            'min_events_attended_90d' => 'integer',
            'required_qualification_ids' => 'array',
            'required_credential_ids' => 'array',
        ];
    }
}
