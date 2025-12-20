<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Enums\FieldType;
use App\Models\Field;
use App\Models\Form;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * @mixin Eloquent|Model
 */
trait HasCustomFieldData
{
    use VirtualColumn;

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return (new self)->getFillable();
    }

    protected static function bootHasCustomFieldData(): void
    {
        static::retrieved(function ($model): void {
            foreach ($model->data ?? [] as $key => $value) {
                if (is_string($value) && self::isFilePath($value)) {
                    $model->setAttribute($key.'_url', Storage::providesTemporaryUrls()
                        ? Storage::temporaryUrl($value, now()->addHour())
                        : Storage::url($value));
                }
            }
        });

        static::saving(function ($model): void {
            $model->handleFileUploads();
        });
    }

    protected static function isFilePath($value): bool
    {
        return is_string($value)
            && ! Str::startsWith($value, ['http://', 'https://'])
            && preg_match('/\.[a-zA-Z0-9]{1,10}$/', $value);
    }

    protected function handleFileUploads(): void
    {
        if (! method_exists($this, 'form')) {
            return;
        }

        /** @var ?Form $form */
        $form = $this->form;
        if (! $form) {
            return;
        }

        /** @var Collection<Field> $fileFields */
        $fileFields = $form->fields()->where('type', FieldType::FIELD_FILE)->get();

        foreach ($fileFields as $field) {
            $value = $this->getAttribute($field->key);

            if ($value instanceof UploadedFile) {
                $path = $value->storePublicly();
                $this->setAttribute($field->key, $path);
            }
        }
    }

    protected function initializeHasCustomFieldData(): void
    {
        $this->guard([]);
        $this->mergeHidden([
            'data',
        ]);
    }
}
