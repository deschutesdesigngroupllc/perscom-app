<?php

namespace App\Prompts;

use App\Models\CombatRecord;
use Illuminate\Support\Str;

class CombatRecordPrompts
{
    /**
     * @return string
     */
    public function created(CombatRecord $record, $type = 'headline')
    {
        $prompt = config("openai.prompts.combatrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);

        return $prompt;
    }
}
