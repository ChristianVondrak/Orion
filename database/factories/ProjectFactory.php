<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->catchPhrase(),
            'status' => $this->faker->randomElement([0, 1]), // 0 = Inactive, 1 = Active
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
