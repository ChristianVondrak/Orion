<?php

namespace Database\Factories;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDetail>
 */
class UserDetailFactory extends Factory
{
    protected $model = UserDetail::class;

    public function definition(): array
    {
        $genders = ['Male', 'Female'];
        $gender = $this->faker->randomElement($genders);

        return [
            'user_id' => null, // será asignado desde el seeder
            'country' => $this->fake->country(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
            'gender' => $gender,
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-22 years')->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
