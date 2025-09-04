<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\ContentTagParser;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string $name
 * @property string|null $description
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read User|null $author
 * @property-read Collection<int, AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read Collection<int, Award> $awards
 * @property-read int|null $awards_count
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Collection<int, CombatRecord> $combat_records
 * @property-read int|null $combat_records_count
 * @property-read string $label
 * @property-read Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read Collection<int, QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read Collection<int, RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read string|null $relative_url
 * @property-read Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read Collection<int, ServiceRecord> $service_records
 * @property-read int|null $service_records_count
 * @property-read Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read string|null $url
 *
 * @method static Builder<static>|Document author(\App\Models\User $user)
 * @method static \Database\Factories\DocumentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Document newModelQuery()
 * @method static Builder<static>|Document newQuery()
 * @method static Builder<static>|Document query()
 * @method static Builder<static>|Document whereAuthorId($value)
 * @method static Builder<static>|Document whereContent($value)
 * @method static Builder<static>|Document whereCreatedAt($value)
 * @method static Builder<static>|Document whereDescription($value)
 * @method static Builder<static>|Document whereId($value)
 * @method static Builder<static>|Document whereName($value)
 * @method static Builder<static>|Document whereUpdatedAt($value)
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

    protected $fillable = [
        'name',
        'description',
        'content',
        'created_at',
        'updated_at',
    ];

    public function toHtml(?User $user = null, mixed $attachedModel = null): string
    {
        return ContentTagParser::parse($this->content, $user, $attachedModel) ?? '';
    }
}
