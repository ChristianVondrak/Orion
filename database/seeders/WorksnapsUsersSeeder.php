<?php

namespace Database\Seeders;

use App\Models\worksnapUser;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WorksnapUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $i) {
            worksnapUser::create([
                'login' => $faker->userName(),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'timezone_id' => $faker->randomElement([1, 2, 3, 4]),
                'timezone_name' => $faker->timezone(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
