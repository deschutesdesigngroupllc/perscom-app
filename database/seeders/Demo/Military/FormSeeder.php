<?php

namespace Database\Seeders\Demo\Military;

use App\Models\Field;
use App\Models\Form;
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
        $forms = [
            [
                'name' => 'Personnel Action Request',
                'description' => 'This form is used primarily for the purpose of requesting or recording personnel actions for or by soldiers in accordance with DA PAM 600-8.',
            ],
            [
                'name' => 'After Action Report',
                'description' => 'An After Action Report (AAR) is a written report that documents a unit\'s actions for historical purposes and provides key observations and lessons learned. It is typically submitted after a training mission, combat operation or other mission.',
            ],
        ];

        $fields = [
            [
                [
                    'name' => 'Date',
                    'key' => 'date',
                    'type' => Field::FIELD_DATE,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_DATE],
                    'cast' => Field::$fieldCasts[Field::FIELD_DATE],
                    'required' => true,
                    'help' => 'Please enter your rank.',
                ],
                [
                    'name' => 'Rank',
                    'key' => 'rank',
                    'type' => Field::FIELD_TEXT,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXT],
                    'cast' => Field::$fieldCasts[Field::FIELD_TEXT],
                    'required' => true,
                    'help' => 'Please enter your rank.',
                    'placeholder' => null,
                ],
                [
                    'name' => 'Remarks',
                    'key' => 'remarks',
                    'type' => Field::FIELD_TEXTAREA,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXTAREA],
                    'cast' => Field::$fieldCasts[Field::FIELD_TEXTAREA],
                    'required' => true,
                    'help' => 'Please provide details of your request.',
                    'placeholder' => null,
                ],
            ],
            [
                [
                    'name' => 'Event Date',
                    'key' => 'event_date',
                    'type' => Field::FIELD_DATETIME,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_DATETIME],
                    'cast' => Field::$fieldCasts[Field::FIELD_DATETIME],
                    'required' => true,
                    'help' => 'Please enter your the date of the event.',
                ],
                [
                    'name' => 'Problems Encountered',
                    'key' => 'problems_encountered',
                    'type' => Field::FIELD_TEXTAREA,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXTAREA],
                    'cast' => Field::$fieldCasts[Field::FIELD_TEXTAREA],
                    'required' => true,
                    'description' => 'Please describe any problems that were encountered.',
                    'placeholder' => null,
                ],
                [
                    'name' => 'Significant Comments',
                    'key' => 'significant_comments',
                    'type' => Field::FIELD_TEXTAREA,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXTAREA],
                    'cast' => Field::$fieldCasts[Field::FIELD_TEXTAREA],
                    'required' => true,
                    'description' => 'Please enter any other significant comments.',
                    'placeholder' => null,
                ],
                [
                    'name' => 'Recommendations For Improvement',
                    'key' => 'recommendations',
                    'type' => Field::FIELD_TEXTAREA,
                    'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXTAREA],
                    'cast' => Field::$fieldCasts[Field::FIELD_TEXTAREA],
                    'required' => true,
                    'description' => 'Please provide any recommendations for improvement.',
                    'placeholder' => null,
                ],
            ],
        ];

        foreach ($forms as $index => $form) {
            $factories = [];

            foreach ($fields[$index] as $field) {
                $factories[] = Field::factory()->state($field)->create();
            }

            Form::factory()->state($form)->hasAttached($factories)->create();
        }
    }
}
