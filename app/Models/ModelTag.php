<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ModelTagType;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * @property int $id
 * @property string|null $tag
 * @property string|null $description
 * @property ModelTagType|null $type
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelTag whereType($value)
 *
 * @mixin \Eloquent
 */
class ModelTag extends Model
{
    use Sushi;

    /**
     * @return array<array<string,string>>
     */
    protected function getRows(): array
    {
        return [
            [
                'tag' => '{user_name}',
                'description' => 'The user\'s name.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_email}',
                'description' => 'The user\'s email.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_email_verified_at}',
                'description' => 'The user\'s email verification date. Null if email has not been verified',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_status}',
                'description' => 'The user\'s status.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_online}',
                'description' => 'The user\'s online status.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_assignment_position}',
                'description' => 'The user\'s current assignment position.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_assignment_specialty}',
                'description' => 'The user\'s current assignment specialty.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_assignment_unit}',
                'description' => 'The user\'s current assignment unit.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_rank}',
                'description' => 'The user\'s current rank.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_rank_abbreviation}',
                'description' => 'The user\'s current rank as an abbreviation.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{user_discord_tag}',
                'description' => 'This will replace the user\'s name with their Discord name and tag them.',
                'type' => ModelTagType::USER,
            ],
            [
                'tag' => '{assignment_record_status}',
                'description' => 'The status of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_unit}',
                'description' => 'The unit of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_position}',
                'description' => 'The position of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_speciality}',
                'description' => 'The specialty of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_type}',
                'description' => 'The type of assignment record, either Primary or Secondary.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_text}',
                'description' => 'The text of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{assignment_record_date}',
                'description' => 'The date of the assignment record.',
                'type' => ModelTagType::ASSIGNMENT_RECORD,
            ],
            [
                'tag' => '{award_record_award}',
                'description' => 'The award of the award record.',
                'type' => ModelTagType::AWARD_RECORD,
            ],
            [
                'tag' => '{award_record_award_image}',
                'description' => 'The award image of the award record.',
                'type' => ModelTagType::AWARD_RECORD,
            ],
            [
                'tag' => '{award_record_text}',
                'description' => 'The text of the award record.',
                'type' => ModelTagType::AWARD_RECORD,
            ],
            [
                'tag' => '{award_record_date}',
                'description' => 'The date of the award record.',
                'type' => ModelTagType::AWARD_RECORD,
            ],
            [
                'tag' => '{combat_record_text}',
                'description' => 'The text of the combat record.',
                'type' => ModelTagType::COMBAT_RECORD,
            ],
            [
                'tag' => '{combat_record_date}',
                'description' => 'The date of the combat record.',
                'type' => ModelTagType::COMBAT_RECORD,
            ],
            [
                'tag' => '{qualification_record_qualification}',
                'description' => 'The qualification of the qualification record.',
                'type' => ModelTagType::QUALIFICATION_RECORD,
            ],
            [
                'tag' => '{qualification_record_qualification_image}',
                'description' => 'The qualification image of the qualification record.',
                'type' => ModelTagType::QUALIFICATION_RECORD,
            ],
            [
                'tag' => '{qualification_record_text}',
                'description' => 'The text of the qualification record.',
                'type' => ModelTagType::QUALIFICATION_RECORD,
            ],
            [
                'tag' => '{qualification_record_date}',
                'description' => 'The date of the qualification record.',
                'type' => ModelTagType::QUALIFICATION_RECORD,
            ],
            [
                'tag' => '{rank_record_rank}',
                'description' => 'The rank of the rank record.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{rank_record_rank_abbreviation}',
                'description' => 'The rank of the rank record as an abbreviation.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{rank_record_rank_image}',
                'description' => 'The rank image of the rank record.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{rank_record_type}',
                'description' => 'The type of rank record, either Promotion Demotion, Lateral, or Transfer.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{rank_record_text}',
                'description' => 'The text of the rank record.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{rank_record_date}',
                'description' => 'The date of the rank record.',
                'type' => ModelTagType::RANK_RECORD,
            ],
            [
                'tag' => '{service_record_text}',
                'description' => 'The text of the service record.',
                'type' => ModelTagType::SERVICE_RECORD,
            ],
            [
                'tag' => '{service_record_date}',
                'description' => 'The date of the service record.',
                'type' => ModelTagType::SERVICE_RECORD,
            ],
            [
                'tag' => '{training_record_credentials}',
                'description' => 'A comma-delimited list of training credentials that were earned.',
                'type' => ModelTagType::TRAINING_RECORD,
            ],
            [
                'tag' => '{training_record_instructor_name}',
                'description' => 'The name of the training instructor.',
                'type' => ModelTagType::TRAINING_RECORD,
            ],
            [
                'tag' => '{training_record_text}',
                'description' => 'The text of the training record.',
                'type' => ModelTagType::TRAINING_RECORD,
            ],
            [
                'tag' => '{training_record_date}',
                'description' => 'The date of the training record.',
                'type' => ModelTagType::TRAINING_RECORD,
            ],
            [
                'tag' => '{author_resource_name}',
                'description' => 'The author\'s name if linked to a resource.',
                'type' => ModelTagType::USER,
            ],
        ];
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => ModelTagType::class,
        ];
    }
}
