<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAuthor;
use App\Traits\HasResourceLabel;
use Database\Factories\CommentFactory;
use Eloquent;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $author
 * @property-read Model|Eloquent|null $commentable
 * @property-read string $label
 *
 * @method static Builder<static>|Comment author(User $user)
 * @method static CommentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Comment newModelQuery()
 * @method static Builder<static>|Comment newQuery()
 * @method static Builder<static>|Comment query()
 * @method static Builder<static>|Comment whereAuthorId($value)
 * @method static Builder<static>|Comment whereComment($value)
 * @method static Builder<static>|Comment whereCreatedAt($value)
 * @method static Builder<static>|Comment whereId($value)
 * @method static Builder<static>|Comment whereModelId($value)
 * @method static Builder<static>|Comment whereModelType($value)
 * @method static Builder<static>|Comment whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Comment extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAuthor;
    use HasFactory;
    use HasResourceLabel;

    protected $fillable = [
        'model_id',
        'model_type',
        'comment',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo('model');
    }
}
