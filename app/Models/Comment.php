<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAuthor;
use App\Traits\HasResourceLabel;
use Eloquent;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $author
 * @property-read Model|Eloquent|null $commentable
 * @property-read string $label
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment author(\App\Models\User $user)
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
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
