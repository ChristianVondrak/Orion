<?php


use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Project;
use App\Models\projectUser;
use App\Models\Timming;
use App\Models\worksnapUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol administrador
        Role::factory()->create([
            'name' => 'Administrator',
            'description' => 'Total privileges and absolute control.',
        ]);

        // Crear usuario administrador
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => 'admin1234',
            'role_id' => 1,
        ]);

        // Crear worksnap users
        $worksnapUsers = worksnapUser::factory(20)->create();

        // Crear detalles de usuario para worksnap users
        $worksnapUsers->each(function ($user) {
            UserDetail::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        // Crear proyectos
        $projects = Project::factory(10)->create();

        // Asignar worksnap users a proyectos
        foreach ($worksnapUsers as $user) {
            $assignedProjects = $projects->random(rand(1, 3));
            foreach ($assignedProjects as $project) {
                projectUser::factory()->create([
                    'user_id' => $user->id,         // relación con worksnap_user
                    'project_id' => $project->id,
                ]);
            }
        }

        // Crear registros de tiempo (timmings) por usuario-proyecto
        projectUser::all()->each(function ($assignment) {
            Timming::factory()->count(rand(2, 5))->create([
                'user_id' => $assignment->user_id,     // worksnap_user.id
                'project_id' => $assignment->project_id,
            ]);
        });
    }
}


