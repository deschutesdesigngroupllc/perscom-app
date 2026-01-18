<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Event;
use App\Models\Message;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Submission;
use App\Models\User;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Carbon;
use ReflectionClass;

enum AutomationTrigger: string implements HasLabel
{
    case ASSIGNMENT_RECORD_CREATED = 'assignmentrecord.created';
    case ASSIGNMENT_RECORD_UPDATED = 'assignmentrecord.updated';
    case ASSIGNMENT_RECORD_DELETED = 'assignmentrecord.deleted';
    case AWARD_RECORD_CREATED = 'awardrecord.created';
    case AWARD_RECORD_UPDATED = 'awardrecord.updated';
    case AWARD_RECORD_DELETED = 'awardrecord.deleted';
    case CALENDAR_CREATED = 'calendar.created';
    case CALENDAR_UPDATED = 'calendar.updated';
    case CALENDAR_DELETED = 'calendar.deleted';
    case COMBAT_RECORD_CREATED = 'combatrecord.created';
    case COMBAT_RECORD_UPDATED = 'combatrecord.updated';
    case COMBAT_RECORD_DELETED = 'combatrecord.deleted';
    case EVENT_CREATED = 'event.created';
    case EVENT_UPDATED = 'event.updated';
    case EVENT_DELETED = 'event.deleted';
    case MESSAGE_CREATED = 'message.created';
    case MESSAGE_UPDATED = 'message.updated';
    case MESSAGE_DELETED = 'message.deleted';
    case QUALIFICATION_RECORD_CREATED = 'qualificationrecord.created';
    case QUALIFICATION_RECORD_UPDATED = 'qualificationrecord.updated';
    case QUALIFICATION_RECORD_DELETED = 'qualificationrecord.deleted';
    case RANK_RECORD_CREATED = 'rankrecord.created';
    case RANK_RECORD_UPDATED = 'rankrecord.updated';
    case RANK_RECORD_DELETED = 'rankrecord.deleted';
    case SERVICE_RECORD_CREATED = 'servicerecord.created';
    case SERVICE_RECORD_UPDATED = 'servicerecord.updated';
    case SERVICE_RECORD_DELETED = 'servicerecord.deleted';
    case SUBMISSION_CREATED = 'submission.created';
    case SUBMISSION_UPDATED = 'submission.updated';
    case SUBMISSION_DELETED = 'submission.deleted';
    case USER_CREATED = 'user.created';
    case USER_UPDATED = 'user.updated';
    case USER_DELETED = 'user.deleted';

    /**
     * @return array<string, array<string, string|null>>
     */
    public static function groupedOptions(): array
    {
        return [
            'User' => [
                self::USER_CREATED->value => self::USER_CREATED->getLabel(),
                self::USER_UPDATED->value => self::USER_UPDATED->getLabel(),
                self::USER_DELETED->value => self::USER_DELETED->getLabel(),
            ],
            'Assignment Record' => [
                self::ASSIGNMENT_RECORD_CREATED->value => self::ASSIGNMENT_RECORD_CREATED->getLabel(),
                self::ASSIGNMENT_RECORD_UPDATED->value => self::ASSIGNMENT_RECORD_UPDATED->getLabel(),
                self::ASSIGNMENT_RECORD_DELETED->value => self::ASSIGNMENT_RECORD_DELETED->getLabel(),
            ],
            'Award Record' => [
                self::AWARD_RECORD_CREATED->value => self::AWARD_RECORD_CREATED->getLabel(),
                self::AWARD_RECORD_UPDATED->value => self::AWARD_RECORD_UPDATED->getLabel(),
                self::AWARD_RECORD_DELETED->value => self::AWARD_RECORD_DELETED->getLabel(),
            ],
            'Combat Record' => [
                self::COMBAT_RECORD_CREATED->value => self::COMBAT_RECORD_CREATED->getLabel(),
                self::COMBAT_RECORD_UPDATED->value => self::COMBAT_RECORD_UPDATED->getLabel(),
                self::COMBAT_RECORD_DELETED->value => self::COMBAT_RECORD_DELETED->getLabel(),
            ],
            'Qualification Record' => [
                self::QUALIFICATION_RECORD_CREATED->value => self::QUALIFICATION_RECORD_CREATED->getLabel(),
                self::QUALIFICATION_RECORD_UPDATED->value => self::QUALIFICATION_RECORD_UPDATED->getLabel(),
                self::QUALIFICATION_RECORD_DELETED->value => self::QUALIFICATION_RECORD_DELETED->getLabel(),
            ],
            'Rank Record' => [
                self::RANK_RECORD_CREATED->value => self::RANK_RECORD_CREATED->getLabel(),
                self::RANK_RECORD_UPDATED->value => self::RANK_RECORD_UPDATED->getLabel(),
                self::RANK_RECORD_DELETED->value => self::RANK_RECORD_DELETED->getLabel(),
            ],
            'Service Record' => [
                self::SERVICE_RECORD_CREATED->value => self::SERVICE_RECORD_CREATED->getLabel(),
                self::SERVICE_RECORD_UPDATED->value => self::SERVICE_RECORD_UPDATED->getLabel(),
                self::SERVICE_RECORD_DELETED->value => self::SERVICE_RECORD_DELETED->getLabel(),
            ],
            'Calendar' => [
                self::CALENDAR_CREATED->value => self::CALENDAR_CREATED->getLabel(),
                self::CALENDAR_UPDATED->value => self::CALENDAR_UPDATED->getLabel(),
                self::CALENDAR_DELETED->value => self::CALENDAR_DELETED->getLabel(),
            ],
            'Event' => [
                self::EVENT_CREATED->value => self::EVENT_CREATED->getLabel(),
                self::EVENT_UPDATED->value => self::EVENT_UPDATED->getLabel(),
                self::EVENT_DELETED->value => self::EVENT_DELETED->getLabel(),
            ],
            'Message' => [
                self::MESSAGE_CREATED->value => self::MESSAGE_CREATED->getLabel(),
                self::MESSAGE_UPDATED->value => self::MESSAGE_UPDATED->getLabel(),
                self::MESSAGE_DELETED->value => self::MESSAGE_DELETED->getLabel(),
            ],
            'Submission' => [
                self::SUBMISSION_CREATED->value => self::SUBMISSION_CREATED->getLabel(),
                self::SUBMISSION_UPDATED->value => self::SUBMISSION_UPDATED->getLabel(),
                self::SUBMISSION_DELETED->value => self::SUBMISSION_DELETED->getLabel(),
            ],
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ASSIGNMENT_RECORD_CREATED => 'Assignment Record Created',
            self::ASSIGNMENT_RECORD_UPDATED => 'Assignment Record Updated',
            self::ASSIGNMENT_RECORD_DELETED => 'Assignment Record Deleted',
            self::AWARD_RECORD_CREATED => 'Award Record Created',
            self::AWARD_RECORD_UPDATED => 'Award Record Updated',
            self::AWARD_RECORD_DELETED => 'Award Record Deleted',
            self::CALENDAR_CREATED => 'Calendar Created',
            self::CALENDAR_UPDATED => 'Calendar Updated',
            self::CALENDAR_DELETED => 'Calendar Deleted',
            self::COMBAT_RECORD_CREATED => 'Combat Record Created',
            self::COMBAT_RECORD_UPDATED => 'Combat Record Updated',
            self::COMBAT_RECORD_DELETED => 'Combat Record Deleted',
            self::EVENT_CREATED => 'Event Created',
            self::EVENT_UPDATED => 'Event Updated',
            self::EVENT_DELETED => 'Event Deleted',
            self::MESSAGE_CREATED => 'Message Created',
            self::MESSAGE_UPDATED => 'Message Updated',
            self::MESSAGE_DELETED => 'Message Deleted',
            self::QUALIFICATION_RECORD_CREATED => 'Qualification Record Created',
            self::QUALIFICATION_RECORD_UPDATED => 'Qualification Record Updated',
            self::QUALIFICATION_RECORD_DELETED => 'Qualification Record Deleted',
            self::RANK_RECORD_CREATED => 'Rank Record Created',
            self::RANK_RECORD_UPDATED => 'Rank Record Updated',
            self::RANK_RECORD_DELETED => 'Rank Record Deleted',
            self::SERVICE_RECORD_CREATED => 'Service Record Created',
            self::SERVICE_RECORD_UPDATED => 'Service Record Updated',
            self::SERVICE_RECORD_DELETED => 'Service Record Deleted',
            self::SUBMISSION_CREATED => 'Submission Created',
            self::SUBMISSION_UPDATED => 'Submission Updated',
            self::SUBMISSION_DELETED => 'Submission Deleted',
            self::USER_CREATED => 'User Created',
            self::USER_UPDATED => 'User Updated',
            self::USER_DELETED => 'User Deleted',
        };
    }

    public function getModelClass(): string
    {
        return match ($this) {
            self::ASSIGNMENT_RECORD_CREATED,
            self::ASSIGNMENT_RECORD_UPDATED,
            self::ASSIGNMENT_RECORD_DELETED => AssignmentRecord::class,

            self::AWARD_RECORD_CREATED,
            self::AWARD_RECORD_UPDATED,
            self::AWARD_RECORD_DELETED => AwardRecord::class,

            self::CALENDAR_CREATED,
            self::CALENDAR_UPDATED,
            self::CALENDAR_DELETED => Calendar::class,

            self::COMBAT_RECORD_CREATED,
            self::COMBAT_RECORD_UPDATED,
            self::COMBAT_RECORD_DELETED => CombatRecord::class,

            self::EVENT_CREATED,
            self::EVENT_UPDATED,
            self::EVENT_DELETED => Event::class,

            self::MESSAGE_CREATED,
            self::MESSAGE_UPDATED,
            self::MESSAGE_DELETED => Message::class,

            self::QUALIFICATION_RECORD_CREATED,
            self::QUALIFICATION_RECORD_UPDATED,
            self::QUALIFICATION_RECORD_DELETED => QualificationRecord::class,

            self::RANK_RECORD_CREATED,
            self::RANK_RECORD_UPDATED,
            self::RANK_RECORD_DELETED => RankRecord::class,

            self::SERVICE_RECORD_CREATED,
            self::SERVICE_RECORD_UPDATED,
            self::SERVICE_RECORD_DELETED => ServiceRecord::class,

            self::SUBMISSION_CREATED,
            self::SUBMISSION_UPDATED,
            self::SUBMISSION_DELETED => Submission::class,

            self::USER_CREATED,
            self::USER_UPDATED,
            self::USER_DELETED => User::class,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function getExampleContext(): array
    {
        $modelClass = $this->getModelClass();
        $modelFields = $this->getModelFields($modelClass);
        ksort($modelFields);

        $causer = [
            'email' => 'john@example.com',
            'id' => 1,
            'name' => 'John Doe',
        ];

        return [
            'now' => Carbon::now(),
            'model' => $modelFields,
            'model_type' => $modelClass,
            'model_id' => $modelFields['id'] ?? null,
            'causer' => $causer,
            'causer_id' => $causer['id'],
            'changes' => null,
        ];
    }

    private function getModelFields(string $modelClass): array
    {
        $reflection = new ReflectionClass($modelClass);
        $docComment = $reflection->getDocComment();

        if ($docComment === false) {
            return ['id' => 1];
        }

        $fields = ['id' => 1];

        // Parse @property annotations from docblock
        preg_match_all('/@property(?:-read)?\s+([^\s]+)\s+\$(\w+)/', $docComment, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = $match[1];
            $name = $match[2];

            // Skip relationships and complex types
            if (str_contains($type, 'Collection') || str_contains($type, '|null')) {
                $type = str_replace('|null', '', $type);
            }

            $fields[$name] = $this->getExampleValueForType($type, $name);
        }

        return $fields;
    }

    private function getExampleValueForType(string $type, string $name): mixed
    {
        if ($name === 'id' || str_ends_with($name, '_id')) {
            return 1;
        }

        if (str_contains($name, 'email')) {
            return 'user@example.com';
        }

        if (str_contains($name, 'name')) {
            return 'Example Name';
        }

        if (str_contains($name, 'date') || str_contains($name, '_at')) {
            return '2026-01-14T12:00:00Z';
        }

        if (str_contains($name, 'url') || str_contains($name, 'link')) {
            return 'https://example.com';
        }

        return match ($type) {
            'int', 'integer' => 1,
            'float', 'double' => 1.0,
            'bool', 'boolean' => true,
            'string' => 'example',
            'array' => [],
            'Carbon', Carbon::class => '2026-01-14T12:00:00Z',
            default => 'value',
        };
    }
}
