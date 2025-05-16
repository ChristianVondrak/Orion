<?php

namespace Database\Seeders;

use App\Models\Timming;
use App\Models\projectUser;
use Illuminate\Database\Seeder;

class TimmingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        dd('Entrando al seeder de timmings');
        // Para cada relación usuario-proyecto, generar de 2 a 5 registros de tiempo
        projectUser::all()->each(function ($assignment) {
            Timming::factory()->count(rand(2, 5))->create([
                'user_id' => $assignment->user_id,
                'project_id' => $assignment->project_id,
            ]);
        });
    }
}
