<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;

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

        return tap($record, function (AssignmentRecord $record) {
            $user = $record->user;

            if (blank($user)) {
                return null;
            }

            if (filled($record->position_id)) {
                $user->forceFill([
                    'position_id' => $record->position_id,
                ])->save();
            }

            if (filled($record->specialty_id)) {
                $user->forceFill([
                    'specialty_id' => $record->specialty_id,
                ])->save();
            }

            if (filled($record->unit_id)) {
                $user->forceFill([
                    'unit_id' => $record->unit_id,
                ])->save();
            }

            if (filled($record->status_id)) {
                $user->forceFill([
                    'status_id' => $record->status_id,
                ])->save();
            }

            if (filled($record->unit_slot)) {
                $user->forceFill([
                    'unit_id' => $record->unit_slot->unit_id ?? null,
                    'specialty_id' => $record->unit_slot->slot->position_id ?? null,
                    'position_id' => $record->unit_slot->slot->specialty_id ?? null,
                    'unit_slot_id' => $record->unit_slot_id,
                ])->save();
            }
        });
    }
}
