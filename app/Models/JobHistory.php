<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $job
 * @property \Illuminate\Support\Carbon $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory whereJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobHistory whereUpdatedAt($value)
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

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'finished_at' => 'datetime',
        ];
    }
}
