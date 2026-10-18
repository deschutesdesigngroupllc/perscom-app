<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Livewire\Widgets\Roster;

use App\Livewire\Widgets\Roster\Index;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class IndexTest extends TenantTestCase
{
    public function test_the_embeddable_roster_widget_still_renders_units_and_members(): void
    {
        $group = Group::factory()->createQuietly(['name' => 'Widget Group']);
        $parent = Unit::factory()->createQuietly(['name' => 'Widget Company']);
        $child = Unit::factory()->createQuietly(['name' => 'Widget Platoon', 'parent_id' => $parent->getKey()]);
        $group->units()->attach([$parent->getKey(), $child->getKey()]);

        User::factory()->createQuietly(['name' => 'Widget Member', 'unit_id' => $child->getKey()]);

        Livewire::test(Index::class)
            ->assertOk()
            ->assertSee('Widget Group')
            ->assertSee('Widget Company')
            ->assertSee('Widget Platoon')
            ->assertSee('Widget Member');
    }
}
