<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $resource
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Award> $awards
 * @property-read int|null $awards_count
 * @property-read Collection<int, Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read Collection<int, Document> $documents
 * @property-read int|null $documents_count
 * @property-read Collection<int, Form> $forms
 * @property-read int|null $forms_count
 * @property-read string $label
 * @property-read Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read Collection<int, Rank> $ranks
 * @property-read int|null $ranks_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category whereCreatedAt($value)
 * @method static Builder<static>|Category whereDescription($value)
 * @method static Builder<static>|Category whereId($value)
 * @method static Builder<static>|Category whereName($value)
 * @method static Builder<static>|Category whereResource($value)
 * @method static Builder<static>|Category whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
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
            ->where('resource', Award::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competencies_categories')
            ->where('resource', Competency::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'documents_categories')
            ->where('resource', Document::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function forms(): BelongsToMany
    {
        return $this->belongsToMany(Form::class, 'forms_categories')
            ->where('resource', Form::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'qualifications_categories')
            ->where('resource', Qualification::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function ranks(): BelongsToMany
    {
        return $this->belongsToMany(Rank::class, 'ranks_categories')
            ->where('resource', Rank::class)
            ->withPivot('order')
            ->withTimestamps();
    }
}
