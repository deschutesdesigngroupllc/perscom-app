<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Sushi\Sushi;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $path
 * @property string|null $size
 * @property string|null $url
 * @property string|null $created_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Backup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Backup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Backup query()
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Backup extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return collect(Storage::disk('s3')->files('backups'))->map(function ($path) {
            $file = pathinfo($path, PATHINFO_FILENAME);
            $date = preg_replace('/\.\w+$/', '', $file);

            return [
                'name' => "$file.zip",
                'path' => $path,
                'size' => Number::fileSize(Storage::disk('s3')->size($path)),
                'url' => Storage::disk('s3')->temporaryUrl($path, now()->addDay()),
                'created_at' => Carbon::createFromFormat('Y-m-d-H-i-s', $date),
            ];
        })->toArray();
    }
}
