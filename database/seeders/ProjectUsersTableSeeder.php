<?php

namespace Database\Seeders;

use App\Models\projectUser;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        // Assign each user to 1–3 random projects
        foreach ($users as $user) {
            $projectsToAssign = $projects->random(rand(1, 3));

            foreach ($projectsToAssign as $project) {
                projectUser::factory()->create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                ]);
            }
        }
    }
}
