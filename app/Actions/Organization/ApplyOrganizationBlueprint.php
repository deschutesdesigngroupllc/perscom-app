<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Data\Organization\AwardData;
use App\Data\Organization\CredentialData;
use App\Data\Organization\FieldData;
use App\Data\Organization\FormData;
use App\Data\Organization\GroupData;
use App\Data\Organization\OrganizationBlueprintData;
use App\Data\Organization\PositionData;
use App\Data\Organization\QualificationData;
use App\Data\Organization\RankData;
use App\Data\Organization\SlotData;
use App\Data\Organization\SpecialtyData;
use App\Data\Organization\StatusData;
use App\Data\Organization\UnitData;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApplyOrganizationBlueprint
{
    public const array SECTIONS = [
        'positions',
        'specialties',
        'ranks',
        'statuses',
        'groups',
        'units',
        'slots',
        'awards',
        'qualifications',
        'credentials',
        'forms',
    ];

    /**
     * Create every entity described by the blueprint in a single transaction. Entities that
     * already exist (matched by name) are reused, so applying a blueprint twice is safe.
     *
     * @return array<string, array{created: int, existing: int}>
     *
     * @throws ValidationException
     * @throws Throwable
     */
    public static function handle(OrganizationBlueprintData $blueprint): array
    {
        $summary = collect(self::SECTIONS)
            ->mapWithKeys(fn (string $section): array => [$section => ['created' => 0, 'existing' => 0]])
            ->all();

        $track = function (string $section, Model $model) use (&$summary): Model {
            $summary[$section][$model->wasRecentlyCreated ? 'created' : 'existing']++;

            return $model;
        };

        DB::transaction(function () use ($blueprint, $track): void {
            $units = collect();

            $positions = collect($blueprint->positions)
                ->mapWithKeys(fn (PositionData $position): array => [
                    Str::lower($position->name) => $track('positions', CreatePosition::handle($position->toArray())),
                ]);

            $specialties = collect($blueprint->specialties)
                ->mapWithKeys(fn (SpecialtyData $specialty): array => [
                    Str::lower($specialty->name) => $track('specialties', CreateSpecialty::handle($specialty->toArray())),
                ]);

            collect($blueprint->ranks)->each(fn (RankData $rank): Model => $track('ranks', CreateRank::handle($rank->toArray())));
            collect($blueprint->statuses)->each(fn (StatusData $status): Model => $track('statuses', CreateStatus::handle($status->toArray())));

            collect($blueprint->groups)->each(function (GroupData $groupData) use ($track, $positions, $specialties, $units): void {
                $group = $track('groups', CreateGroup::handle([
                    'name' => $groupData->name,
                    'description' => $groupData->description,
                    'icon' => $groupData->icon,
                ]));

                collect($groupData->units)->each(function (UnitData $unitData) use ($track, $group, $positions, $specialties, $units): void {
                    $unit = $track('units', CreateUnit::handle([
                        'name' => $unitData->name,
                        'description' => $unitData->description,
                    ], $group));

                    $units->put(Str::lower($unitData->name), $unit);

                    collect($unitData->slots)->each(fn (SlotData $slot): Model => $track('slots', CreateSlot::handle([
                        'name' => $slot->name,
                        'description' => $slot->description,
                        'position_id' => filled($slot->position) ? ($positions->get(Str::lower($slot->position)) ?? Position::query()->whereRaw('LOWER(name) = ?', [Str::lower($slot->position)])->first())?->getKey() : null,
                        'specialty_id' => filled($slot->specialty) ? ($specialties->get(Str::lower($slot->specialty)) ?? Specialty::query()->whereRaw('LOWER(name) = ?', [Str::lower($slot->specialty)])->first())?->getKey() : null,
                    ], $unit)));
                });
            });

            collect($blueprint->groups)
                ->flatMap(fn (GroupData $groupData): array => $groupData->units)
                ->filter(fn (UnitData $unitData): bool => filled($unitData->parent))
                ->each(fn (UnitData $unitData) => self::nestUnit($units->get(Str::lower($unitData->name)), (string) $unitData->parent, $units));

            collect($blueprint->awards)->each(fn (AwardData $award): Model => $track('awards', CreateAward::handle($award->toArray())));
            collect($blueprint->qualifications)->each(fn (QualificationData $qualification): Model => $track('qualifications', CreateQualification::handle($qualification->toArray())));
            collect($blueprint->credentials)->each(fn (CredentialData $credential): Model => $track('credentials', CreateCredential::handle($credential->toArray())));

            collect($blueprint->forms)->each(fn (FormData $form): Model => $track('forms', CreateForm::handle([
                'name' => $form->name,
                'description' => $form->description,
                'instructions' => $form->instructions,
                'success_message' => $form->successMessage,
                'is_public' => $form->isPublic,
                'fields' => collect($form->fields)->map(fn (FieldData $field): array => $field->toArray())->all(),
            ])));
        });

        return $summary;
    }

    /**
     * Units are created before they are nested so a parent can appear anywhere in the blueprint. A
     * parent that is missing, the unit itself, or one of its own descendants is skipped.
     *
     * @param  Collection<string, Unit>  $units
     */
    protected static function nestUnit(?Unit $unit, string $parentName, Collection $units): void
    {
        $parent = $units->get(Str::lower($parentName))
            ?? Unit::query()->withoutGlobalScopes()->whereRaw('LOWER(name) = ?', [Str::lower($parentName)])->first();

        if (! $unit instanceof Unit || ! $parent instanceof Unit || $parent->is($unit) || $unit->parent_id === $parent->getKey()) {
            return;
        }

        $ancestorId = $parent->parent_id;
        $visited = [$parent->getKey()];

        while ($ancestorId !== null && ! in_array($ancestorId, $visited, true)) {
            if ($ancestorId === $unit->getKey()) {
                return;
            }

            $visited[] = $ancestorId;
            $ancestorId = Unit::query()->withoutGlobalScopes()->whereKey($ancestorId)->value('parent_id');
        }

        $unit->forceFill(['parent_id' => $parent->getKey()])->save();
    }
}
