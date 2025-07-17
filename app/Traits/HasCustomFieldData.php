<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * @mixin Eloquent
 */
trait HasCustomFieldData
{
    use VirtualColumn;

    protected static function bootHasCustomFieldData(): void
    {
        static::retrieved(function ($model): void {
            foreach ($model->data ?? [] as $key => $value) {
                if (is_string($value) && self::isFilePath($value)) {
                    $model->setAttribute("{$key}_url", Storage::url($value));
                }
            }
        });
    }

    protected static function isFilePath($value): bool
    {
        return is_string($value)
            && ! Str::startsWith($value, ['http://', 'https://'])
            && preg_match('/\.[a-zA-Z0-9]{1,10}$/', $value);
    }

    protected function initializeHasCustomFieldData(): void
    {
        $this->guard([]);
        $this->setHidden(array_merge($this->getHidden(), [
            'data',
        ]));
    }
}
