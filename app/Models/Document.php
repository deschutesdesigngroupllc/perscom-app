<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\ModelTextParserService;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasAuthor;
use App\Traits\HasAwardRecords;
use App\Traits\HasCategories;
use App\Traits\HasCombatRecords;
use App\Traits\HasQualificationRecords;
use App\Traits\HasRankRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasServiceRecords;
use App\Traits\HasTags;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string $name
 * @property string|null $description
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read User|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CombatRecord> $combat_records
 * @property-read int|null $combat_records_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceRecord> $service_records
 * @property-read int|null $service_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Support\Optional|string|null|null $url
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Document author(\App\Models\User $user)
 * @method static \Database\Factories\DocumentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Document withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Document extends Model implements HasLabel, Htmlable
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAssignmentRecords;
    use HasAuthor;
    use HasAwardRecords;
    use HasCategories;
    use HasCombatRecords;
    use HasFactory;
    use HasQualificationRecords;
    use HasRankRecords;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasServiceRecords;
    use HasTags;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'content',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function toHtml(?User $user = null, mixed $attachedModel = null): string
    {
        return ModelTextParserService::parse($this->content, $user, $attachedModel) ?? '';
    }
}
