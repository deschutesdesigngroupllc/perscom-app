<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Eloquent;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string $filename
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string|null $image_url
 * @property-read string $label
 * @property-read Model|Eloquent|null $model
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Support\Optional|string|null|null $url
 *
 * @method static \Database\Factories\ImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Image withoutTrashed()
 *
 * @mixin Eloquent
 */
class Image extends Model implements HasLabel
{
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'filename',
        'path',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['image_url'];

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->path ? Storage::disk('s3')->url($this->path) : null
        );
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    protected static function booted(): void
    {
        static::deleting(fn (Image $model) => Storage::disk('s3')->delete($model->path));
    }
}
