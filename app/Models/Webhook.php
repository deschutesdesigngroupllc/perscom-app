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
 * @property WebhookMethod $method
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $logs
 * @property-read int|null $logs_count
 *
 * @method static \Database\Factories\WebhookFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook query()
 *
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use Actionable;
    use HasFactory;

    /**
     * @var string[]
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
