<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Eloquent;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
 * @property-read string|null $image_url
 * @property-read string $label
 * @property-read Model|Eloquent|null $model
 * @property-read string|null $model_url
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static \Database\Factories\ImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Image extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
        'filename',
        'path',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['image_url'];

    /**
     * @return Attribute<?string, never>
     */
    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->path ? Storage::url($this->path) : null
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function modelUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (blank($this->model)) {
                return null;
            }

            /** @var resource $resource */
            $resource = Filament::getModelResource($this->model);

            if (blank($resource)) {
                return null;
            }

            if (array_key_exists('view', $resource::getPages())) {
                return $resource::getUrl('view', [
                    'record' => $this->model,
                ]);
            }

            if (array_key_exists('edit', $resource::getPages())) {
                return $resource::getUrl('edit', [
                    'record' => $this->model,
                ]);
            }

            return null;
        })->shouldCache();
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    protected static function booted(): void
    {
        static::deleting(fn (Image $model) => Storage::delete($model->path));
    }
}
