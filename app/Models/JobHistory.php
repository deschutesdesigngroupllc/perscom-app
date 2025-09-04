<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $job
 * @property Carbon $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|JobHistory newModelQuery()
 * @method static Builder<static>|JobHistory newQuery()
 * @method static Builder<static>|JobHistory query()
 * @method static Builder<static>|JobHistory whereCreatedAt($value)
 * @method static Builder<static>|JobHistory whereFinishedAt($value)
 * @method static Builder<static>|JobHistory whereId($value)
 * @method static Builder<static>|JobHistory whereJob($value)
 * @method static Builder<static>|JobHistory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class JobHistory extends Model
{
    use HasFactory;

    protected $table = 'job_history';

    protected $fillable = [
        'job',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'finished_at' => 'datetime',
        ];
    }
}
