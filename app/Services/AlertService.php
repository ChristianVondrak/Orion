<?php

namespace App\Services;

use App\Models\PlannedProjectHour;
use App\Models\PlannedUserHour;
use App\Models\Project;
use App\Models\User;
use App\Models\worksnapUser;
use App\Notifications\UserHourDeviationNotification;
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
    /**
     * Check each professional’s weekly hours vs planned and notify admins
     * if the deviation exceeds 10%.
     *
     * @return void
     */
    public function checkUserHourDeviation(): void
    {
        $start     = Carbon::now()->subWeek()->startOfWeek();
        $end       = Carbon::now()->subWeek()->endOfWeek();
        $weekStart = $start->toDateString();

        // 1) Obtener administradores
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->get();

        // 2) Traer usuarios con email válido,
        //    con conteo de timmings y relación de plannedHours filtrada
        worksnapUser::whereNotNull('email')
            ->where('email', '<>', '')
            ->withCount([
                // Cuenta sólo timmings entre $start y $end
                'timmings as timmings_count' => function($q) use($start, $end) {
                    $q->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp]);
                },
            ])
            ->with([
                // Trae sólo el registro de planned_hours para esa semana
                'plannedHours' => fn($q) => $q->where('week_start', $weekStart)
            ])
            // Si tienes muchos usuarios, usa chunkById(100) para no colapsar memoria
            ->chunkById(100, function($users) use($admins) {
                foreach ($users as $worker) {
                    // 3) Plan: si hay registro, usarlo; si no, default 5 días * 8 h = 40 h
                    $planRecord = $worker->plannedHours->first();
                    $plan       = $planRecord
                        ? (float)$planRecord->planned_hours
                        : 5 * 8.0;

                    if ($plan <= 0) {
                        continue;
                    }

                    // 4) Actual en horas: timmings_count * 10 minutos → horas
                    $actual = round(($worker->timmings_count * 10) / 60, 2);

                    // 5) Desviación %
                    $dev = round((($actual - $plan) / $plan) * 100, 2);

                    if (abs($dev) > 10) {
                        Notification::send(
                            $admins,
                            new UserHourDeviationNotification($worker, $plan, $actual, $dev)
                        );
                    }
                }
            });
    }
}

