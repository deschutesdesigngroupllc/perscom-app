<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Database\Seeders;

use App\Models\Unit;
use Database\Seeders\FireServiceSeeder;
use Database\Seeders\LawEnforcementSeeder;
use Database\Seeders\MilitarySeeder;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Tenant\TenantTestCase;

class OrganizationSeederHierarchyTest extends TenantTestCase
{
    /**
     * @return array<string, array{class-string<MilitarySeeder|FireServiceSeeder|LawEnforcementSeeder>}>
     */
    public static function seeders(): array
    {
        return [
            'military' => [MilitarySeeder::class],
            'fire service' => [FireServiceSeeder::class],
            'law enforcement' => [LawEnforcementSeeder::class],
        ];
    }

    /**
     * @param  class-string<MilitarySeeder|FireServiceSeeder|LawEnforcementSeeder>  $seeder
     */
    #[DataProvider('seeders')]
    public function test_seeded_units_are_nested_under_their_parents(string $seeder): void
    {
        $names = collect($seeder::UNIT_HIERARCHY)->flatMap(fn (string $parent, string $child): array => [$child, $parent])->unique();

        $units = $names->map(fn (string $name): Unit => Unit::factory()->createQuietly(['name' => $name]));

        resolve($seeder)->nestUnits(new Collection($units->all()), $seeder::UNIT_HIERARCHY);

        foreach ($seeder::UNIT_HIERARCHY as $child => $parent) {
            $this->assertSame($parent, Unit::query()->where('name', $child)->firstOrFail()->parent?->name, $child);
        }

        $roots = $names->diff(array_keys($seeder::UNIT_HIERARCHY));

        $this->assertCount(1, $roots, 'Each organization should have a single top-level unit.');
        $this->assertNull(Unit::query()->where('name', $roots->first())->firstOrFail()->parent_id);
    }
}
