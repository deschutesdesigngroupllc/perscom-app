<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $resource
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Award> $awards
 * @property-read int|null $awards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Form> $forms
 * @property-read int|null $forms_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Rank> $ranks
 * @property-read int|null $ranks_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
        'resource',
        'created_at',
        'updated_at',
    ];

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'awards_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'documents_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function forms(): BelongsToMany
    {
        return $this->belongsToMany(Form::class, 'forms_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'qualifications_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function ranks(): BelongsToMany
    {
        return $this->belongsToMany(Rank::class, 'ranks_categories')
            ->withPivot('order')
            ->withTimestamps();
    }
}
