<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class AssignmentRecordService
{
    use Conditionable;

    public static function process(AssignmentRecord $record): ?AssignmentRecord
    {
        if (blank($record->user)) {
            return $record;
        }

        if ($record->type !== AssignmentRecordType::PRIMARY) {
            return $record;
        }

        $handler = function (string $attribute, mixed $value, AssignmentRecord $record) {
            $user = $record->user;

            if (blank($user)) {
                return null;
            }

            return value(match ($attribute) {
                'position_id' => $user->update([
                    'position_id' => $record->position_id,
                ]),
                'specialty_id' => $user->update([
                    'specialty_id' => $record->specialty_id,
                ]),
                'unit_id' => $user->update([
                    'unit_id' => $record->unit_id,
                ]),
                'status_id' => $user->update([
                    'status_id' => $record->status_id,
                ]),
                'unit_slot_id' => function () use ($record, $user) {
                    if (filled($record->unit_slot)) {
                        $user->update([
                            'unit_id' => $record->unit_slot->unit_id ?? null,
                            'specialty_id' => $record->unit_slot->slot->position_id ?? null,
                            'position_id' => $record->unit_slot->slot->specialty_id ?? null,
                        ]);
                    }
                },
                default => null
            });
        };

        Collection::wrap($record->getDirty())
            ->each(fn (mixed $value, string $key) => value($handler, $key, $value, $record));

        return $record;
    }
}
