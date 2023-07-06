<?php

namespace App\Prompts;

use App\Models\QualificationRecord;
use Illuminate\Support\Str;

class QualificationRecordPrompts
{
    public function created(QualificationRecord $record, $type = 'headline')
    {
        $prompt = config("openai.prompts.qualificationrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);
        $prompt = Str::replace('{qualification}', $record->qualification->name, $prompt);
        $prompt = Str::replace('{description}', $record->qualification->description, $prompt);

        return $prompt;
    }
}
