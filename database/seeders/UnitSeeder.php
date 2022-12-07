<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::factory()->count(10)->sequence(fn ($sequence) => [
            'name' => 'Unit '.$sequence->index + 1,
            'order' => $sequence->index + 1,
        ])->create();
    }
}
