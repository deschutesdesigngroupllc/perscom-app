<?php

namespace App\Prompts;

use App\Models\AssignmentRecord;
use Illuminate\Support\Str;

class AssignmentRecordPrompts
{
    /**
     * @return string
     */
    public function created(AssignmentRecord $record, $type = 'headline')
    {
        $prompt = config("openai.prompts.assignmentrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);
        $prompt = Str::replace('{unit}', $record->unit->name, $prompt);
        $prompt = Str::replace('{unit_description}', $record->unit->description, $prompt);
        $prompt = Str::replace('{position}', $record->position->name, $prompt);
        $prompt = Str::replace('{position_description}', $record->position->description, $prompt);
        $prompt = Str::replace('{specialty}', $record->specialty->name, $prompt);
        $prompt = Str::replace('{specialty_description}', $record->specialty->description, $prompt);

        return $prompt;
    }
}
