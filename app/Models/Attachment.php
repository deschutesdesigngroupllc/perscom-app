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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string $filename
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string|null $attachment_url
 * @property-read string $label
 * @property-read Model|Eloquent|null $model
 * @property-read string|null $model_url
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Support\Optional|string|null|null $url
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
 * @mixin Eloquent
 */
class Attachment extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'filename',
        'model_type',
        'model_id',
        'path',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['attachment_url'];

    /**
     * @return Attribute<?string, void>
     */
    public function attachmentUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->path ? Storage::url($this->path) : null
        );
    }

    /**
     * @return Attribute<?string, void>
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
        static::deleting(fn (Attachment $model) => Storage::delete($model->path));
    }
}
