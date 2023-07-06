<?php

namespace App\Prompts;

use App\Models\ServiceRecord;
use Illuminate\Support\Str;

class ServiceRecordPrompts
{
    public function created(ServiceRecord $record, $type = 'headline')
    {
        $prompt = config("openai.prompts.servicerecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);

        return $prompt;
    }
}
