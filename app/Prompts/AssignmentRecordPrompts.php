<?php

namespace App\Prompts;

use App\Models\AssignmentRecord;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Support\Str;

class AssignmentRecordPrompts
{
    public function created(AssignmentRecord $record, string $type = 'headline'): string
    {
        $prompt = config("openai.prompts.assignmentrecord.created.$type");

        $prompt = Str::replace('{user}', $record->user->name, $prompt);
        $prompt = Str::replace('{text}', $record->text, $prompt);

        $status = optional($record->status, function (Status $status) use ($prompt) {
            return Str::replace('{status}', $status->name, $prompt);
        });

        if ($status) {
            $prompt = $status;
        }

        $unit = optional($record->unit, function (Unit $unit) use ($prompt) {
            $prompt = Str::replace('{unit}', $unit->name, $prompt);

            return Str::replace('{unit_description}', $unit->description, $prompt);
        });

        if ($unit) {
            $prompt = $unit;
        }

        $specialty = optional($record->specialty, function (Specialty $specialty) use ($prompt) {
            $prompt = Str::replace('{specialty}', $specialty->name, $prompt);

            return Str::replace('{specialty_description}', $specialty->description, $prompt);
        });

        if ($specialty) {
            $prompt = $specialty;
        }

        $position = optional($record->position, function (Position $position) use ($prompt) {
            $prompt = Str::replace('{position}', $position->name, $prompt);

            return Str::replace('{position_description}', $position->description, $prompt);
        });

        if ($position) {
            $prompt = $position;
        }

        return $prompt;
    }
}
