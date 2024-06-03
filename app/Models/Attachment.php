<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property string $name
 * @property string $filename
 * @property string $model_type
 * @property int $model_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $model
 *
 * @method static \Database\Factories\AttachmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
