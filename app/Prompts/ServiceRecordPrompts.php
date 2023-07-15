<?php

namespace App\Prompts;

use App\Models\ServiceRecord;
use Illuminate\Support\Str;

class ServiceRecordPrompts
{
    public function created(ServiceRecord $record, string $type = 'headline'): string
    {
        $prompt = config("openai.prompts.servicerecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);

        return Str::replace('{text}', $record->text, $prompt);
    }
}
