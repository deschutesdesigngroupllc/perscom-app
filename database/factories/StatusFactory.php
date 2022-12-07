<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	$status = $this->faker->randomElement($this->possibleStatus());

        return [
            'name' => $status['name'],
            'color' => $status['color'],
        ];
    }

	/**
	 * @return array
	 */
    protected function possibleStatus()
    {
    	return [
    		[
    			'name' => 'Active',
			    'color' => 'bg-green-100 text-green-600'
		    ],
		    [
		    	'name' => 'Inactive',
			    'color' => 'bg-red-100 text-red-600',
		    ],
		    [
			    'name' => 'On Leave',
			    'color' => 'bg-sky-100 text-sky-600',
		    ],
	    ];
    }
}
