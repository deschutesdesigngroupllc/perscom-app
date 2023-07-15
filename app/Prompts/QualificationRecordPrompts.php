<?php

namespace App\Prompts;

use App\Models\QualificationRecord;
use Illuminate\Support\Str;

class QualificationRecordPrompts
{
    public function created(QualificationRecord $record, string $type = 'headline'): string
    {
        $prompt = config("openai.prompts.qualificationrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);
        $prompt = Str::replace('{qualification}', $record->qualification->name, $prompt);

        return Str::replace('{description}', $record->qualification->description, $prompt);
    }
}
