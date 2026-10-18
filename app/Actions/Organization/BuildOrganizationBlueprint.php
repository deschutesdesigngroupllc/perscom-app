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
use App\Models\Award;
use App\Models\Credential;
use App\Models\Enums\FieldOptionsType;
use App\Models\Field;
use App\Models\Form;
use App\Models\Group;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Settings\OrganizationSettings;
use BackedEnum;

class BuildOrganizationBlueprint
{
    /**
     * Describe the organization's current structure as a blueprint that can be exported or re-applied.
     */
    public static function handle(): OrganizationBlueprintData
    {
        /** @var OrganizationSettings $settings */
        $settings = resolve(OrganizationSettings::class);

        return new OrganizationBlueprintData(
            name: $settings->name,
            positions: Position::query()->reorder()->orderBy('order')->orderBy('id')->get()
                ->map(fn (Position $position): PositionData => new PositionData(
                    name: $position->name,
                    description: $position->description,
                ))->all(),
            specialties: Specialty::query()->reorder()->orderBy('order')->orderBy('id')->get()
                ->map(fn (Specialty $specialty): SpecialtyData => new SpecialtyData(
                    name: $specialty->name,
                    abbreviation: $specialty->abbreviation,
                    description: $specialty->description,
                ))->all(),
            ranks: Rank::query()->withoutGlobalScopes()->with('categories')->orderBy('order')->orderBy('id')->get()
                ->map(fn (Rank $rank): RankData => new RankData(
                    name: $rank->name,
                    abbreviation: $rank->abbreviation,
                    paygrade: $rank->paygrade,
                    description: $rank->description,
                    category: $rank->categories->first()?->name,
                ))->all(),
            statuses: Status::query()->reorder()->orderBy('order')->orderBy('id')->get()
                ->map(fn (Status $status): StatusData => new StatusData(
                    name: $status->name,
                    color: $status->color,
                ))->all(),
            groups: Group::query()->reorder()->with('units.parent', 'units.slots.position', 'units.slots.specialty')->orderBy('order')->orderBy('id')->get()
                ->map(fn (Group $group): GroupData => new GroupData(
                    name: $group->name,
                    description: $group->description,
                    icon: $group->icon,
                    units: $group->units->map(fn (Unit $unit): UnitData => new UnitData(
                        name: $unit->name,
                        description: $unit->description,
                        parent: $unit->parent?->name,
                        slots: $unit->slots->map(fn (Slot $slot): SlotData => new SlotData(
                            name: $slot->name,
                            description: $slot->description,
                            position: $slot->position?->name,
                            specialty: $slot->specialty?->name,
                        ))->all(),
                    ))->all(),
                ))->all(),
            awards: Award::query()->reorder()->with('categories')->orderBy('order')->orderBy('id')->get()
                ->map(fn (Award $award): AwardData => new AwardData(
                    name: $award->name,
                    description: $award->description,
                    category: $award->categories->first()?->name,
                ))->all(),
            qualifications: Qualification::query()->reorder()->with('categories')->orderBy('order')->orderBy('id')->get()
                ->map(fn (Qualification $qualification): QualificationData => new QualificationData(
                    name: $qualification->name,
                    description: $qualification->description,
                    category: $qualification->categories->first()?->name,
                ))->all(),
            credentials: Credential::query()->reorder()->with('issuer')->orderBy('order')->orderBy('id')->get()
                ->map(fn (Credential $credential): CredentialData => new CredentialData(
                    name: $credential->name,
                    description: $credential->description,
                    type: $credential->type instanceof BackedEnum ? (string) $credential->type->value : (string) $credential->type,
                    issuer: $credential->issuer?->name,
                ))->all(),
            forms: Form::query()->with('fields')->orderBy('id')->get()
                ->map(fn (Form $form): FormData => new FormData(
                    name: $form->name,
                    description: $form->description,
                    instructions: $form->instructions,
                    successMessage: $form->success_message,
                    isPublic: (bool) $form->is_public,
                    fields: $form->fields->map(fn (Field $field): FieldData => new FieldData(
                        name: $field->name,
                        type: $field->type instanceof BackedEnum ? (string) $field->type->value : (string) $field->type,
                        required: (bool) $field->required,
                        placeholder: $field->placeholder,
                        help: $field->help,
                        options: $field->options_type === FieldOptionsType::ARRAY
                            ? collect($field->options ?? [])->values()->map(fn (mixed $option): string => (string) $option)->all()
                            : [],
                    ))->all(),
                ))->all(),
        );
    }
}
