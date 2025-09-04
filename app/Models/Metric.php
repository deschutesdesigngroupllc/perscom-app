<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MetricFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $key
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static MetricFactory factory($count = null, $state = [])
 * @method static Builder<static>|Metric newModelQuery()
 * @method static Builder<static>|Metric newQuery()
 * @method static Builder<static>|Metric query()
 * @method static Builder<static>|Metric whereCount($value)
 * @method static Builder<static>|Metric whereCreatedAt($value)
 * @method static Builder<static>|Metric whereId($value)
 * @method static Builder<static>|Metric whereKey($value)
 * @method static Builder<static>|Metric whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Metric extends Model
{
    use CentralConnection;
    use HasFactory;

    protected $fillable = [
        'key',
        'count',
        'created_at',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }
}
