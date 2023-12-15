<?php

namespace App\Models;

use App\Traits\HasTags;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * App\Models\Document
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\DocumentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document tags(?mixed $tag)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Document extends Model implements Htmlable
{
    use HasFactory;
    use HasTags;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'content', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    public static $availableTags = [
        '{user_name}' => 'The user\'s name.',
        '{user_email}' => 'The user\'s email.',
        '{user_email_verified_at}' => 'The user\'s email verification date. Null if email has not been verified',
        '{user_status}' => 'The user\'s status.',
        '{user_online}' => 'The user\'s online status.',
        '{user_assignment_position}' => 'The user\'s current assignment position.',
        '{user_assignment_specialty}' => 'The user\'s current assignment specialty.',
        '{user_assignment_unit}' => 'The user\'s current assignment unit.',
        '{user_rank}' => 'The user\'s current rank.',
        '{assignment_record_unit}' => 'The unit of the assignment record.',
        '{assignment_record_position}' => 'The position of the assignment record.',
        '{assignment_record_speciality}' => 'The specialty of the assignment record.',
        '{assignment_record_text}' => 'The text of the assignment record.',
        '{assignment_record_date}' => 'The date of the assignment record.',
        '{award_record_award}' => 'The award of the award record.',
        '{award_record_text}' => 'The text of the award record.',
        '{award_record_date}' => 'The date of the award record.',
        '{combat_record_text}' => 'The text of the combat record.',
        '{combat_record_date}' => 'The date of the combat record.',
        '{qualification_record_qualification}' => 'The qualification of the qualification record.',
        '{qualification_record_text}' => 'The text of the qualification record.',
        '{qualification_record_date}' => 'The date of the qualification record.',
        '{rank_record_rank}' => 'The qualification of the rank record.',
        '{rank_record_type}' => 'The type of rank record, either Promotion or Demotion.',
        '{rank_record_text}' => 'The text of the rank record.',
        '{rank_record_date}' => 'The date of the rank record.',
        '{service_record_text}' => 'The text of the service record.',
        '{service_record_date}' => 'The date of the service record.',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'documents_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    protected function resolveTag(string $tag, ?User $user = null, mixed $attachedModel = null): mixed
    {
        return match (true) {
            $tag === '{user_name}' => $user->name ?? null,
            $tag === '{user_email}' => $user->email ?? null,
            $tag === '{user_email_verified_at}' => optional($user)->email_verified_at ? Carbon::parse($user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => $user->status->name ?? null,
            $tag === '{user_online}' => $user->online ?? null,
            $tag === '{user_assignment_position}' => $user->position->name ?? null,
            $tag === '{user_assignment_specialty}' => $user->specialty->name ?? null,
            $tag === '{user_assignment_unit}' => $user->unit->name ?? null,
            $tag === '{user_rank}' => $user->rank->name ?? null,
            $tag === '{assignment_record_unit}' => $attachedModel->unit->name ?? null,
            $tag === '{assignment_record_position}' => $attachedModel->position->name ?? null,
            $tag === '{assignment_record_speciality}' => $attachedModel->specialty->name ?? null,
            $tag === '{assignment_record_text}' => $attachedModel->text ?? null,
            $tag === '{assignment_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{award_record_award}' => $attachedModel->award->name ?? null,
            $tag === '{award_record_text}' => $attachedModel->text ?? null,
            $tag === '{award_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{combat_record_text}' => $attachedModel->text ?? null,
            $tag === '{combat_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{qualification_record_qualification}' => $attachedModel->qualification->name ?? null,
            $tag === '{qualification_record_text}' => $attachedModel->text ?? null,
            $tag === '{qualification_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{rank_record_rank}' => $attachedModel->award->name ?? null,
            $tag === '{rank_record_type}' => $attachedModel->type === RankRecord::RECORD_RANK_PROMOTION ? 'Promotion' : 'Demotion',
            $tag === '{rank_record_text}' => $attachedModel->text ?? null,
            $tag === '{rank_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            $tag === '{service_record_text}' => $attachedModel->text ?? null,
            $tag === '{service_record_date}' => optional($attachedModel)->created_at ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString() : null,
            default => null
        };
    }

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
}
