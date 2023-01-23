<?php

namespace Database\Seeders;

use App\Models\QualificationRecord;
use Illuminate\Database\Seeder;

class QualificationRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QualificationRecord::withoutEvents(function () {
            QualificationRecord::factory()->count(10)->create();
        });
    }
}
