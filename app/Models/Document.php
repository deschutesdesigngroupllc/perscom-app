<?php

namespace App\Models;

use App\Models\Records\Rank;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Tags\HasTags;

class Document extends Model
{
    use HasFactory;
    use HasTags;

    /**
     * @var string[]
     */
    public static $tags = [
        '{user_name}' => 'The user\'s name.',
        '{user_email}' => 'The user\'s email.',
        '{user_email_verified_at}' => 'The user\'s email verification date. Null if email has not been verified',
        '{user_status}' => 'The user\'s status.',
        '{user_online_status}' => 'The user\'s online status.',
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

    /**
     * @param  User  $user
     * @param  null  $attachedModel
     *
     * @return mixed|string
     */
    public function replaceContent(User $user, $attachedModel = null)
    {
        $content = $this->content;
        foreach (self::$tags as $tag => $description) {
            $content = Str::replace($tag, $this->resolveTag($tag, $user, $attachedModel), $content);
        }

        return $content;
    }

    /**
     * @param             $tag
     * @param  User|null  $user
     * @param  null       $attachedModel
     *
     * @return mixed|void|null
     */
    protected function resolveTag($tag, ?User $user = null, $attachedModel = null)
    {
        switch ($tag) {
            case '{user_name}':
                return $user->name ?? null;
                break;
            case '{user_email}':
                return $user->email ?? null;
                break;
            case '{user_email_verified_at}':
                return $user->email_verified_at;
                break;
            case '{user_status}':
                return $user->status->name ?? null;
                break;
            case '{user_online_status}':
                return $user->online_status ?? null;
                break;
            case '{user_assignment_position}':
                return $user->assignment->position->name ?? null;
                break;
            case '{user_assignment_specialty}':
                return $user->assignment->specialty->name ?? null;
                break;
            case '{user_assignment_unit}':
                return $user->assignment->unit->name ?? null;
                break;
            case '{user_rank}':
                return $user->rank->name ?? null;
                break;
            case '{assignment_record_unit}':
                return $attachedModel->unit->name ?? null;
                break;
            case '{assignment_record_position}':
                return $attachedModel->position->name ?? null;
                break;
            case '{assignment_record_speciality}':
                return $attachedModel->specialty->name ?? null;
                break;
            case '{assignment_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{assignment_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            case '{award_record_award}':
                return $attachedModel->award->name ?? null;
                break;
            case '{award_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{award_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            case '{combat_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{combat_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            case '{qualification_record_qualification}':
                return $attachedModel->qualification->name ?? null;
                break;
            case '{qualification_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{qualification_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            case '{rank_record_rank}':
                return $attachedModel->rank->name ?? null;
                break;
            case '{rank_record_type}':
                return $attachedModel->type === Rank::RECORD_RANK_PROMOTION ? 'Promotion' : 'Demotion';
                break;
            case '{rank_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{rank_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            case '{service_record_text}':
                return $attachedModel->text ?? null;
                break;
            case '{service_record_date}':
                return $attachedModel->created_at
                    ? Carbon::parse($attachedModel->created_at)->toDayDateTimeString()
                    : null;
                break;
            default:
                return null;
        }
    }
}
