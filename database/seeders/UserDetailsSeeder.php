<?php

namespace Database\Seeders;

use App\Models\worksnapUser;
use Illuminate\Database\Seeder;
use App\Models\UserDetail;
use Faker\Factory as Faker;

class UserDetailsSeeder extends Seeder
{

    public function run(){

        $faker = Faker::create();
        // Fetch all users without details
        $usersWithoutDetails = worksnapUser::doesntHave('detail')->get();

        foreach ($usersWithoutDetails as $user) {
            UserDetail::create([
                'user_id'        => $user->id,
                'country'        => $faker->country(),
                'phone'          => $faker->e164PhoneNumber(),
                'position'       => $faker->jobTitle(),
                'gender'         => $faker->randomElement(['male','female','other']),
                'marital_status' => $faker->randomElement(['single','married','divorced','widowed']),
                'date_of_birth'  => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            ]);
        }
    }
}
