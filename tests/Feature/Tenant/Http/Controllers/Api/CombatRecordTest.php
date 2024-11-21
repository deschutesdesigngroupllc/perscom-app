<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\CombatRecords\CombatRecordsController;
use App\Models\CombatRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class CombatRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'combat-records';
    }

    public function controller(): string
    {
        return CombatRecordsController::class;
    }

    public function model(): string
    {
        return CombatRecord::class;
    }

    /**
     * @return Factory<CombatRecord>
     */
    public function factory(): Factory
    {
        return CombatRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:combatrecord',
            'show' => 'view:combatrecord',
            'store' => 'create:combatrecord',
            'update' => 'update:combatrecord',
            'delete' => 'delete:combatrecord',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'text' => $this->faker->paragraph,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'text' => $this->faker->paragraph,
        ];
    }
}
