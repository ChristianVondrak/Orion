<?php

namespace App\Services;

use App\Models\PlannedProjectHour;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectHourDeviationNotification;
use Carbon\Carbon;

class AlertService
{
    /**
     * Comprueba cada proyecto comparando horas planificadas vs. reales
     * y notifica a todos los administradores si la desviación supera el 10%.
     */
    public function checkProjectHourDeviation(): void
    {
        $start = Carbon::now()->subWeek()->startOfWeek();
        $end   = Carbon::now()->subWeek()->endOfWeek();

        // Obtener todos los administradores (usuarios cuyo role.name = 'Administrator')
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))
            ->get();

        foreach (Project::all() as $project) {
            // Horas planificadas (heredando si no hay registro exacto)
            $plan = PlannedProjectHour::getForWeek($project->id, $start);

            // Cálculo de horas reales basadas en bloques de 10' (timmings)
            $seconds = $project->timmings()
                    ->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp])
                    ->count() * 10 * 60;
            $actual = round($seconds / 3600, 2);

            if ($plan > 0) {
                $deviation = round((($actual - $plan) / $plan) * 100, 2);

                if (abs($deviation) > 10) {
                    // Enviar notificación a todos los administradores
                    Notification::send(
                        $admins,
                        new ProjectHourDeviationNotification(
                            $project,
                            $plan,
                            $actual,
                            $deviation
                        )
                    );
                }
            }
        }
    }
}

