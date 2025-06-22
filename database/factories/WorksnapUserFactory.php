<?php

namespace Database\Factories;

use App\Models\worksnapUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<worksnapUser>
 */
class WorksnapUserFactory extends Factory
{
    protected $model = worksnapUser::class;

    public function definition(): array
    {
        return [
            'login' => $this->faker->unique()->userName(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'timezone_id' => $this->faker->numberBetween(1, 10),
            'timezone_name' => $this->faker->timezone(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
