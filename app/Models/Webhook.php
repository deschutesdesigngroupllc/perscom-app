<?php

namespace App\Models;

use App\Models\Enums\WebhookMethod;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Webhook extends Model
{
    use Actionable;

    /**
     * @var string[]
     */
    protected $fillable = ['url', 'description', 'events', 'method'];

    /**
     * @var string[]
     */
    protected $casts = [
        'events' => 'array',
        'method' => WebhookMethod::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function logs()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
