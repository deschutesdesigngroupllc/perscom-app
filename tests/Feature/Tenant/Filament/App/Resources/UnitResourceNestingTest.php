<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\UnitResource\Pages\EditUnit;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class UnitResourceNestingTest extends TenantTestCase
{
    protected Unit $battalion;

    protected Unit $company;

    protected Unit $platoon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->battalion = Unit::factory()->createQuietly(['name' => 'Battalion']);
        $this->company = Unit::factory()->createQuietly(['name' => 'Company', 'parent_id' => $this->battalion->getKey()]);
        $this->platoon = Unit::factory()->createQuietly(['name' => 'Platoon', 'parent_id' => $this->company->getKey()]);

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_units_resolve_their_parent_children_and_descendants(): void
    {
        $this->assertTrue($this->company->parent->is($this->battalion));
        $this->assertTrue($this->battalion->children->contains($this->company));
        $this->assertEqualsCanonicalizing(
            [$this->company->getKey(), $this->platoon->getKey()],
            $this->battalion->descendantIds(),
        );
    }

    public function test_a_unit_can_be_nested_beneath_another_unit(): void
    {
        $standalone = Unit::factory()->createQuietly(['name' => 'Standalone']);

        Livewire::test(EditUnit::class, ['record' => $standalone->getKey()])
            ->fillForm(['parent_id' => $this->company->getKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($this->company->getKey(), $standalone->refresh()->parent_id);
    }

    public function test_a_unit_cannot_be_nested_beneath_its_own_descendant(): void
    {
        Livewire::test(EditUnit::class, ['record' => $this->battalion->getKey()])
            ->fillForm(['parent_id' => $this->platoon->getKey()])
            ->call('save')
            ->assertHasFormErrors(['parent_id']);

        $this->assertNull($this->battalion->refresh()->parent_id);
    }

    public function test_a_unit_cannot_be_its_own_parent(): void
    {
        Livewire::test(EditUnit::class, ['record' => $this->company->getKey()])
            ->fillForm(['parent_id' => $this->company->getKey()])
            ->call('save')
            ->assertHasFormErrors(['parent_id']);

        $this->assertSame($this->battalion->getKey(), $this->company->refresh()->parent_id);
    }

    public function test_deleting_a_parent_unit_moves_its_children_to_the_top_level(): void
    {
        $this->battalion->delete();

        $this->assertNull($this->company->refresh()->parent_id);
    }
}
