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
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory whereJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobHistory whereUpdatedAt($value)
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
