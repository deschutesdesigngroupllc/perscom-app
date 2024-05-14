<?php

namespace App\Models;

use App\Models\Enums\WebhookMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Nova\Actions\Actionable;

/**
 * App\Models\Webhook
 *
 * @property int $id
 * @property string $url
 * @property string|null $description
 * @property WebhookMethod $method
 * @property array $events
 * @property string $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $logs
 * @property-read int|null $logs_count
 *
 * @method static \Database\Factories\WebhookFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use Actionable;
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['url', 'description', 'events', 'method'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'events' => 'array',
        'method' => WebhookMethod::class,
    ];

    public function logs(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
