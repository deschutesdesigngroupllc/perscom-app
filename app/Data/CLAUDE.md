# Data Objects

Spatie Laravel Data objects, grouped by domain (`Events/`, `Organization/`, `Promotions/`,
`Readiness/`, and so on). They carry typed values between actions, services, AI agents, and
Filament forms, and hold no persistence of their own. This file covers the organization blueprint
in `Organization/`, which is the largest of them and the one with the most callers.

## Organization Blueprints

`Organization/OrganizationBlueprintData` describes an organization's whole structure: an optional
`name` and `description`, then lists of `PositionData`, `SpecialtyData`, `RankData`, `StatusData`,
`GroupData`, `AwardData`, `QualificationData`, `CredentialData`, and `FormData`. Two sections nest:
groups hold `UnitData`, which hold `SlotData`, and forms hold `FieldData`. Every section defaults
to an empty array, so a partial blueprint is valid.

### Where Blueprints Come From

| Source          | Code                                                            |
|-----------------|-----------------------------------------------------------------|
| Bundled presets | `resources/blueprints/*.json` via `fromPreset()`                |
| AI generation   | `app/Services/Ai/OrganizationBlueprintService.php`              |
| Current tenant  | `app/Actions/Organization/BuildOrganizationBlueprint.php`       |
| Form state      | `OrganizationBlueprint::fromState()` in `app/Forms/Components/` |
| Arbitrary JSON  | `fromJson()`                                                    |

`presets()` globs `resources/blueprints`, keys each file by its basename, and labels it with
`Str::headline()`. `fromPreset()` rejects any name that is not a key of `presets()` before it
builds a path, which is what stops a name like `../composer` from reading another file; keep that
check if the lookup changes. `fromJson()` throws `JsonException` on malformed input.

`OrganizationBlueprintService::toBlueprint()` is the validating entry point for untrusted state (AI
output and form state). It drops unnamed entries and empty arrays, then calls `validateAndCreate()`.
`from()` and `fromJson()` do not validate beyond type casting.

### Applying a Blueprint

`app/Actions/Organization/ApplyOrganizationBlueprint.php` writes a blueprint to the tenant database
in one transaction and returns created and existing counts per entry in its `SECTIONS` constant.

- **Names are identity.** Each `Create*` action in `app/Actions/Organization` finds an existing
  record by case-insensitive name before creating one (a slot also matches on its position and
  specialty), so applying a blueprint twice is safe and renaming an entry in a blueprint creates a
  new record instead of updating the old one.
- **References are by name.** A slot's `position` and `specialty` name a position or specialty in
  the same blueprint or one that already exists; an unknown name leaves the id null. A unit's
  `parent` names a unit from any group in the blueprint or an existing unit. Parents are set after
  every unit exists, and a missing parent, the unit itself, or one of its descendants is skipped.
- **List order is display order for new records.** Ranks are listed most senior first, which
  `PromotionEligibilityService` and the rank API rely on. Positions, specialties, ranks, statuses,
  groups, units, awards, qualifications, and credentials use `CanBeOrdered`, so each new record is
  appended after the existing ones in list order. A matched existing record keeps its place. Forms
  and slots have no `order` column.
- **Form keys are snake case in JSON.** `FormData` maps names with `SnakeCaseMapper`, so preset
  and exported JSON use `success_message` and `is_public`. Every other data object uses its
  property names as written.

### Entry Points

1. **Signup.** The landing register page (`resources/js/landing/pages/Register.jsx`) sends an
   optional `organization_type`, validated by `RegisterRequest` against `OrganizationType` and kept
   on the `Registration`. `VerifyController` passes it to `CreateNewTenant`, which stores it in the
   tenant's `data` column (it is not one of `Tenant::getCustomColumns()`). The queued
   `App\Jobs\System\SetupTenantAccount` job runs `App\Actions\Tenant\SetupTenantAccount`, whose
   `applyOrganizationBlueprint()` applies the preset named by `OrganizationType::blueprintPreset()`
   and then marks `OnboardingSettings` completed so the dashboard setup wizard does not open.
2. **Onboarding wizard.** `app/Filament/App/Pages/Dashboard.php` loads a preset or an AI draft into
   the editable `OrganizationBlueprint` form component and applies it when the wizard finishes.
3. **Organization Builder.** `app/Filament/App/Clusters/Settings/Pages/OrganizationBuilder.php`
   imports a preset, asks the AI to extend the current structure, applies the proposal, and
   exports the current structure as `organization-blueprint.json` through
   `BuildOrganizationBlueprint`. `OrganizationBlueprintService::extend()` strips anything that
   already exists with `withoutExisting()`.

`OrganizationType` (`app/Models/Enums/OrganizationType.php`) lists what a founder can pick at
signup. Its `blueprintPreset()` returns the enum value as a preset name for every case except
`OTHER`, so each non-`OTHER` value must match a file name in `resources/blueprints`.
`tests/Feature/Central/Models/Enums/OrganizationTypeTest.php` enforces that. `SetupTenantAccount`
also skips any type whose preset is missing. `php artisan perscom:install` does not use blueprints;
its `--seeder` option picks one of the `MilitarySeeder`, `FireServiceSeeder`, or
`LawEnforcementSeeder` classes.

### Common Changes

**Add a preset.** Drop a kebab-case JSON file in `resources/blueprints`. It appears in the
Dashboard and Organization Builder preset selects and as an AI starting point with no code change.
Update `test_it_lists_the_bundled_presets` in
`tests/Feature/Tenant/Data/Organization/OrganizationBlueprintDataTest.php`. To offer it at signup,
add an `OrganizationType` case whose value is the file name, with a `getLabel()` entry;
`RegisterController` sends every case to the register page.

**Add a field or section.** A new property has to reach every place that reads or writes the
shape:

- the data class in `Organization/`, and `OrganizationBlueprintData` for a new section
- `ApplyOrganizationBlueprint` (and `SECTIONS` for a new section) and the matching `Create*`
  action's validation
- `BuildOrganizationBlueprint`, so exports round-trip
- `OrganizationBlueprintService` (`NAMED_SECTIONS`, `summarize()`, `withoutExisting()`)
- the agent schema and instructions in `app/Ai/Agents/OrganizationBlueprintAgent.php`
- the `OrganizationBlueprint` form component
- the preset JSON files, when presets should carry it

Tests sit beside each piece under `tests/Feature/Tenant` (`Data/Organization`,
`Actions/Organization`, `Services/Ai`, and the Dashboard and Organization Builder page tests).
