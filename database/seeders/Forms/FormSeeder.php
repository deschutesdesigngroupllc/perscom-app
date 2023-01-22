<?php

namespace Database\Seeders\Forms;

use App\Models\Forms\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Form::factory()->count(10)->createQuietly();
    }
}
