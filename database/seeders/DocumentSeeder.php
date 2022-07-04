<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Document;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Document::factory()->count(10)->create();
    }
}
