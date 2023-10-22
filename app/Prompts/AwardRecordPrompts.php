<?php

namespace App\Prompts;

use App\Models\AwardRecord;
use Illuminate\Support\Str;

class AwardRecordPrompts
{
    public function created(AwardRecord $record, $type = 'headline'): string
    {
        $prompt = config("openai.prompts.awardrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);
        $prompt = Str::replace('{award}', $record->award->name, $prompt);
        $prompt = Str::replace('{description}', $record->award->description, $prompt);

        return $prompt;
    }
}
