<?php

declare(strict_types=1);

namespace App\Models;

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
use Carbon\Carbon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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

    public static array $availableTags = [
        '{user_name}' => 'The user\'s name.',
        '{user_email}' => 'The user\'s email.',
        '{user_email_verified_at}' => 'The user\'s email verification date. Null if email has not been verified',
        '{user_status}' => 'The user\'s status.',
        '{user_online}' => 'The user\'s online status.',
        '{user_assignment_position}' => 'The user\'s current assignment position.',
        '{user_assignment_specialty}' => 'The user\'s current assignment specialty.',
        '{user_assignment_unit}' => 'The user\'s current assignment unit.',
        '{user_rank}' => 'The user\'s current rank.',
        '{assignment_record_status}' => 'The status of the assignment record.',
        '{assignment_record_unit}' => 'The unit of the assignment record.',
        '{assignment_record_position}' => 'The position of the assignment record.',
        '{assignment_record_speciality}' => 'The specialty of the assignment record.',
        '{assignment_record_text}' => 'The text of the assignment record.',
        '{assignment_record_date}' => 'The date of the assignment record.',
        '{award_record_award}' => 'The award of the award record.',
        '{award_record_award_image}' => 'The award image of the award record.',
        '{award_record_text}' => 'The text of the award record.',
        '{award_record_date}' => 'The date of the award record.',
        '{combat_record_text}' => 'The text of the combat record.',
        '{combat_record_date}' => 'The date of the combat record.',
        '{qualification_record_qualification}' => 'The qualification of the qualification record.',
        '{qualification_record_qualification_image}' => 'The qualification image of the qualification record.',
        '{qualification_record_text}' => 'The text of the qualification record.',
        '{qualification_record_date}' => 'The date of the qualification record.',
        '{rank_record_rank}' => 'The rank of the rank record.',
        '{rank_record_rank_image}' => 'The rank image of the rank record.',
        '{rank_record_type}' => 'The type of rank record, either Promotion or Demotion.',
        '{rank_record_text}' => 'The text of the rank record.',
        '{rank_record_date}' => 'The date of the rank record.',
        '{service_record_text}' => 'The text of the service record.',
        '{service_record_date}' => 'The date of the service record.',
        '{author_resource_name}' => 'The author\'s name if linked to a resource.',
        '{author_document_name}' => 'The document author\'s name.',
    ];

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
        $content = $this->content;
        foreach (self::$availableTags as $tag => $description) {
            if ($replacement = $this->resolveTag($tag, $user, $attachedModel)) {
                $content = Str::replace((string) $tag, $replacement, $content);
            }
        }

        return $content;
    }

    protected function resolveTag(string $tag, ?User $user = null, mixed $attachedModel = null): mixed
    {
        return match (true) {
            $tag === '{user_name}' => data_get($user, 'name'),
            $tag === '{user_email}' => $user->email ?? null,
            $tag === '{user_email_verified_at}' => optional($user)->email_verified_at ? Carbon::parse($user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => $user->status->name ?? null,
            $tag === '{user_online}' => optional($user)->online ? 'True' : 'False',
            $tag === '{user_assignment_position}' => $user->position->name ?? null,
            $tag === '{user_assignment_specialty}' => $user->specialty->name ?? null,
            $tag === '{user_assignment_unit}' => $user->unit->name ?? null,
            $tag === '{user_rank}' => $user->rank->name ?? null,
            $tag === '{assignment_record_status}' => $attachedModel->status->name ?? null,
            $tag === '{assignment_record_unit}' => $attachedModel->unit->name ?? null,
            $tag === '{assignment_record_position}' => $attachedModel->position->name ?? null,
            $tag === '{assignment_record_speciality}' => $attachedModel->specialty->name ?? null,
            $tag === '{assignment_record_text}', $tag === '{award_record_text}', $tag === '{combat_record_text}', $tag === '{qualification_record_text}', $tag === '{service_record_text}', $tag === '{rank_record_text}' => $attachedModel->text ?? null,
            $tag === '{assignment_record_date}', $tag === '{award_record_date}', $tag === '{combat_record_date}', $tag === '{qualification_record_date}', $tag === '{service_record_date}', $tag === '{rank_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{award_record_award}' => $attachedModel->award->name ?? null,
            $tag === '{award_record_award_image}' => optional($attachedModel->award->image->image_url ?? null, fn ($url) => new HtmlString("<img src='$url' alt='Image' style='height: 40px;' />")) ?? null,
            $tag === '{qualification_record_qualification}' => $attachedModel->qualification->name ?? null,
            $tag === '{qualification_record_qualification_image}' => optional($attachedModel->qualification->image->image_url ?? null, fn ($url) => new HtmlString("<img src='$url' alt='Image' style='height: 40px;' />")) ?? null,
            $tag === '{rank_record_rank}' => $attachedModel->rank->name ?? null,
            $tag === '{rank_record_rank_image}' => optional($attachedModel->rank->image->image_url ?? null, fn ($url) => new HtmlString("<img src='$url' alt='Image' style='height: 40px;' />")) ?? null,
            $tag === '{rank_record_type}' => optional($attachedModel->type ?? null)->getLabel() ?? null,
            $tag === '{author_resource_name}' => ! is_null($attachedModel) && in_array(HasAuthor::class, class_uses_recursive(get_class($attachedModel))) ? optional($attachedModel->author)->name : null,
            $tag === '{author_document_name}' => optional($this->author)->name,
            default => null
        };
    }
}
