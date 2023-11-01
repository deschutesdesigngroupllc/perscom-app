<?php

namespace App\Prompts;

use App\Models\RankRecord;
use Illuminate\Support\Str;

class RankRecordPrompts
{
    public function created(RankRecord $record, string $type = 'headline'): string
    {
        $prompt = config("openai.prompts.rankrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);
        $prompt = Str::replace('{rank}', $record->rank->name, $prompt);
        $prompt = Str::replace('{description}', $record->rank->description, $prompt);

        return $prompt;
    }
}
