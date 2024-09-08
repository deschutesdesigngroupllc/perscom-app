<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasResourceLabel;
use Eloquent;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $author
 * @property-read Model|Eloquent|null $commentable
 * @property-read string $label
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Comment author(\App\Models\User $user)
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withoutTrashed()
 *
 * @mixin Eloquent
 */
class Comment extends Model implements HasLabel
{
    use HasAuthor;
    use HasFactory;
    use HasResourceLabel;
    use SoftDeletes;

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
