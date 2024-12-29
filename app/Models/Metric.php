<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $key
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\MetricFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Metric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Metric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Metric query()
 * @method static \Illuminate\Database\Eloquent\Builder|Metric whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metric whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metric whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metric whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Metric extends Model
{
    use CentralConnection;
    use HasFactory;

    /**
     * @var array<int, string>
     */
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
