<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\WebhookMethod;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $url
 * @property string|null $description
 * @property WebhookMethod $method
 * @property array $events
 * @property string $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $logs
 * @property-read int|null $logs_count
 *
 * @method static \Database\Factories\WebhookFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use HasFactory;
    use HasLogs;
    use SoftDeletes;

    protected $fillable = [
        'url',
        'description',
        'events',
        'method',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'events' => 'array',
            'method' => WebhookMethod::class,
        ];
    }
}
