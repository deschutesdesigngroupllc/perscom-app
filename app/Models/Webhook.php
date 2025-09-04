<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\WebhookMethod;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $url
 * @property string|null $description
 * @property WebhookMethod $method
 * @property array<array-key, mixed> $events
 * @property string $secret
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 *
 * @method static \Database\Factories\WebhookFactory factory($count = null, $state = [])
 * @method static Builder<static>|Webhook newModelQuery()
 * @method static Builder<static>|Webhook newQuery()
 * @method static Builder<static>|Webhook query()
 * @method static Builder<static>|Webhook whereCreatedAt($value)
 * @method static Builder<static>|Webhook whereDescription($value)
 * @method static Builder<static>|Webhook whereEvents($value)
 * @method static Builder<static>|Webhook whereId($value)
 * @method static Builder<static>|Webhook whereMethod($value)
 * @method static Builder<static>|Webhook whereSecret($value)
 * @method static Builder<static>|Webhook whereUpdatedAt($value)
 * @method static Builder<static>|Webhook whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use HasFactory;
    use HasLogs;

    protected $fillable = [
        'url',
        'description',
        'events',
        'method',
        'secret',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'method' => WebhookMethod::class,
        ];
    }
}
