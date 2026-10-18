<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $invocation_id
 * @property string $agent
 * @property string|null $provider
 * @property string|null $model
 * @property int $input_tokens
 * @property int $output_tokens
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Database\Factories\AiUsageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereInputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereInvocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereOutputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereUserId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class AiUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invocation_id',
        'agent',
        'provider',
        'model',
        'input_tokens',
        'output_tokens',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
        ];
    }
}
