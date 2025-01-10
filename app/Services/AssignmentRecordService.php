<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use Closure;
use Illuminate\Support\Collection;

class AssignmentRecordService
{
    public static function process(AssignmentRecord $record): ?AssignmentRecord
    {
        if (blank($record->user)) {
            return $record;
        }

        if ($record->type !== AssignmentRecordType::PRIMARY) {
            return $record;
        }

        $handler = function (string $attribute, mixed $value, AssignmentRecord $record): bool {
            $user = $record->user;

            if (blank($user)) {
                return false;
            }

            return match ($attribute) {
                'position_id' => $user->update([
                    'position_id' => optional($record->position)->id,
                ]),
                'specialty_id' => $user->update([
                    'specialty_id' => optional($record->specialty)->id,
                ]),
                'unit_id' => $user->update([
                    'unit_id' => optional($record->unit)->id,
                ]),
                'status_id' => $user->update([
                    'status_id' => optional($record->status)->id,
                ]),
                default => null
            };
        };

        Collection::wrap($record->getDirty())
            ->each(fn (mixed $value, string $key): Closure => value($handler, $key, $value, $record));

        return $record;
    }
}
