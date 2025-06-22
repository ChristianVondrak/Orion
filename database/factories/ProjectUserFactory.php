<?php

namespace Database\Factories;

use App\Models\projectUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<projectUser>
 */
class ProjectUserFactory extends Factory
{
    protected $model = projectUser::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // será asignado desde el seeder
            'project_id' => null, // será asignado desde el seeder
            'hourly_rate' => $this->faker->randomElement([0, 10, 15, 20, 30, 0]), // algunos con 0 (fijo)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
