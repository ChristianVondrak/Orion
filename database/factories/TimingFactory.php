<?php

namespace Database\Factories;

use App\Models\Timming;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timming>
 */
class TimmingFactory extends Factory
{
    protected $model = Timming::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $minutes = $this->faker->numberBetween(30, 240); // entre 0.5h y 4h

        return [
            'user_id' => null, // desde seeder
            'project_id' => null, // desde seeder
            'from_timestamp' => $start,
            'minutes' => $minutes,
            'created_at' => $start,
            'updated_at' => $start,
        ];
    }
}
