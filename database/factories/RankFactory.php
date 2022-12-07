<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rank = $this->faker->randomElement($this->possibleRanks());

        return [
            'name' => $rank['name'],
            'description' => $this->faker->paragraph,
            'abbreviation' => $rank['abbreviation'],
            'paygrade' => $rank['paygrade'],
        ];
    }

	/**
	 * @return \string[][]
	 */
    protected function possibleRanks()
    {
    	return [
    		[
    			'name' => 'Private',
			    'abbreviation' => 'PVT',
			    'paygrade' => 'E-1'
		    ],
		    [
			    'name' => 'Private Second Class',
			    'abbreviation' => 'PV2',
			    'paygrade' => 'E-2'
		    ],
		    [
			    'name' => 'Private First Class',
			    'abbreviation' => 'PFC',
			    'paygrade' => 'E-3'
		    ],
		    [
			    'name' => 'Specialist',
			    'abbreviation' => 'SPC',
			    'paygrade' => 'E-4'
		    ],
		    [
			    'name' => 'Corporal',
			    'abbreviation' => 'CPL',
			    'paygrade' => 'E-4'
		    ],
		    [
			    'name' => 'Sergeant',
			    'abbreviation' => 'SGT',
			    'paygrade' => 'E-5'
		    ],
		    [
			    'name' => 'Staff Sergeant',
			    'abbreviation' => 'SSG',
			    'paygrade' => 'E-6'
		    ],
		    [
			    'name' => 'Sergeant First Class',
			    'abbreviation' => 'SFC',
			    'paygrade' => 'E-7'
		    ]
	    ];
    }
}
