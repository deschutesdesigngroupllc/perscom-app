<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Sushi\Sushi;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $path
 * @property int|null $size
 * @property string|null $url
 * @property Carbon|null $created_at
 *
 * @method static Builder|Backup newModelQuery()
 * @method static Builder|Backup newQuery()
 * @method static Builder|Backup query()
 * @method static Builder|Backup whereId($value)
 *
 * @mixin \Eloquent
 */
class Backup extends Model
{
    use Sushi;

    protected $fillable = [
        'name',
        'path',
        'size',
        'url',
        'created_at',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getRows(): array
    {
        return collect(Storage::disk('backups')->files('backups'))->map(function ($path): array {
            $file = pathinfo($path, PATHINFO_FILENAME);
            $date = preg_replace('/\.\w+$/', '', $file);

            return [
                'name' => "$file.zip",
                'path' => $path,
                'size' => Number::fileSize(Storage::disk('backups')->size($path)),
                'url' => Storage::disk('backups')->temporaryUrl($path, now()->addDay()),
                'created_at' => Carbon::createFromFormat('Y-m-d-H-i-s', $date),
            ];
        })->toArray();
    }
}
