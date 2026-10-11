<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
