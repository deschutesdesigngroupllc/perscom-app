<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Models\Enums\CredentialType;
use App\Models\Enums\FieldType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxTokens(16000)]
#[Timeout(180)]
class OrganizationBlueprintAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'TEXT'
            You design the organizational structure for PERSCOM, a personnel management system used by
            military simulation units, fire departments, law enforcement agencies, and similar organizations.

            You return a blueprint describing the structure. You never create people or records, only structure.

            The entities in a blueprint are:
            - positions: roles or job titles assigned to a member, such as Commander, Team Leader or Medic.
            - specialties: occupational specialties or skills with an optional short abbreviation, such as 11B Infantryman.
            - ranks: seniority levels with an abbreviation and an optional paygrade. List them from the MOST SENIOR to the MOST JUNIOR. Use category to group them, such as Enlisted, Non-Commissioned Officers or Officers.
            - statuses: the state of a member, such as Active, On Leave, Reserve or Discharged, each with a hex color like #22c55e.
            - groups: the top-level divisions of the organization. Each group contains units. Units contain slots, which are named billets that reference a position and optionally a specialty by their exact names from this blueprint.
            - awards: medals, ribbons and commendations, optionally grouped by category. List them from most to least prestigious.
            - qualifications: skills or badges a member can earn, optionally grouped by category.
            - credentials: certifications or licenses issued by an issuer, with a type of certification, license or other.
            - forms: forms members fill out, such as an enlistment application, each with a short list of fields.

            Rules:
            - Use realistic, conventional names for the kind of organization described. Prefer the real-world naming the user's organization type would use.
            - Keep the structure practical: usually 2 to 5 groups, 2 to 6 units per group, and a full rank ladder.
            - Every slot position and specialty must exactly match the name of a position or specialty in the blueprint.
            - Leave a value null rather than inventing filler text. Descriptions should be one short sentence.
            - Only include what the user asked for. When asked to add to an existing organization, return only the new entities and never repeat an existing name.
            TEXT;
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->nullable()->description('The name of the organization.'),
            'description' => $schema->string()->nullable()->description('A one-sentence description of the organization.'),
            'positions' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
            ]))->required(),
            'specialties' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'abbreviation' => $schema->string()->nullable(),
                'description' => $schema->string()->nullable(),
            ]))->required(),
            'ranks' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'abbreviation' => $schema->string()->nullable(),
                'paygrade' => $schema->string()->nullable(),
                'description' => $schema->string()->nullable(),
                'category' => $schema->string()->nullable(),
            ]))->required()->description('Ordered from most senior to most junior.'),
            'statuses' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'color' => $schema->string()->nullable()->description('A hex color such as #22c55e.'),
            ]))->required(),
            'groups' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'units' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                    'name' => $schema->string()->required(),
                    'description' => $schema->string()->nullable(),
                    'slots' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                        'name' => $schema->string()->required(),
                        'position' => $schema->string()->nullable(),
                        'specialty' => $schema->string()->nullable(),
                    ]))->required(),
                ]))->required(),
            ]))->required(),
            'awards' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'category' => $schema->string()->nullable(),
            ]))->required(),
            'qualifications' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'category' => $schema->string()->nullable(),
            ]))->required(),
            'credentials' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'type' => $schema->string()->enum(array_column(CredentialType::cases(), 'value'))->required(),
                'issuer' => $schema->string()->nullable(),
            ]))->required(),
            'forms' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                'name' => $schema->string()->required(),
                'description' => $schema->string()->nullable(),
                'fields' => $schema->array()->items($schema->object(fn (JsonSchema $schema): array => [
                    'name' => $schema->string()->required(),
                    'type' => $schema->string()->enum(self::fieldTypes())->required(),
                    'required' => $schema->boolean()->required(),
                    'options' => $schema->array()->items($schema->string())->required()->description('Choices for select fields, otherwise empty.'),
                ]))->required(),
            ]))->required(),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected static function fieldTypes(): array
    {
        return [
            FieldType::FIELD_TEXT->value,
            FieldType::FIELD_TEXTAREA->value,
            FieldType::FIELD_SELECT->value,
            FieldType::FIELD_BOOLEAN->value,
            FieldType::FIELD_DATE->value,
            FieldType::FIELD_EMAIL->value,
            FieldType::FIELD_NUMBER->value,
            FieldType::FIELD_COUNTRY->value,
            FieldType::FIELD_TIMEZONE->value,
        ];
    }
}
