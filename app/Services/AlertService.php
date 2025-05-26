<?php

namespace App\Services;

use App\Models\PlannedProjectHour;
use App\Models\PlannedUserHour;
use App\Models\Project;
use App\Models\Timming;
use App\Models\User;
use App\Models\worksnapUser;
use App\Notifications\UserHourDeviationNotification;
use App\Notifications\UserInactivityNotification;
use App\Notifications\UserPerformanceNotification;
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
        $end = Carbon::now()->subWeek()->endOfWeek();

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
        $start = Carbon::now()->subWeek()->startOfWeek();
        $end = Carbon::now()->subWeek()->endOfWeek();
        $weekStart = $start->toDateString();

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->get();

        worksnapUser::whereNotNull('email')
            ->where('email', '<>', '')
            ->withCount([
                'timmings as timmings_count' => function ($q) use ($start, $end) {
                    $q->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp]);
                },
            ])
            ->with([
                'plannedHours' => fn($q) => $q->where('week_start', $weekStart)
            ])
            ->chunkById(100, function ($users) use ($admins) {
                foreach ($users as $worker) {
                    $planRecord = $worker->plannedHours->first();
                    $plan = $planRecord
                        ? (float)$planRecord->planned_hours
                        : 5 * 8.0;

                    if ($plan <= 0) {
                        continue;
                    }

                    $actual = round(($worker->timmings_count * 10) / 60, 2);
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

    /**
     * Comprueba inactividad de profesionales independientes.
     * — Si no registran timmings en los últimos N días, alerta.
     */
    public function checkUserInactivity(): void
    {
        $days = config('alerts.inactivity_threshold_days', 3);
        $cutoff = now()->subDays($days)->startOfDay()->timestamp;
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->get();

        worksnapUser::whereNotNull('email')
            ->where('email', '<>', '')
            ->withMax(['timmings as last_activity_ts' => fn($q) => $q], 'from_timestamp')
            ->chunkById(100, function ($users) use ($cutoff, $days, $admins) {
                foreach ($users as $worker) {
                    $lastTs = $worker->last_activity_ts;
                    if (!$lastTs || $lastTs < $cutoff) {
                        Notification::send(
                            $admins,
                            new UserInactivityNotification($worker, $days)
                        );
                    }
                }
            });
    }

    public function checkUserPerformance(): void
    {
        $start = Carbon::yesterday()->startOfDay();
        $end = Carbon::yesterday()->endOfDay();
        $low = config('alerts.performance_low_threshold', 75);
        $high = config('alerts.performance_high_threshold', 97);
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->get();

        worksnapUser::whereNotNull('email')
            ->where('email', '<>', '')
            ->chunkById(100, function ($users) use ($start, $end, $low, $high, $admins) {
                foreach ($users as $worker) {
                    // Agrupa sus timmings por proyecto SOLO de ese usuario
                    $timmingsByProject = $worker
                        ->timmings()
                        ->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp])
                        ->with('project')
                        ->get()
                        ->groupBy('project_id');

                    foreach ($timmingsByProject as $projectId => $group) {
                        $project = $group->first()->project;
                        if (!$project) continue;

                        $percent = round($group->avg('activity_level') * 10, 2);
                        if ($percent < $low || $percent > $high) {
                            Notification::send(
                                $admins,
                                new UserPerformanceNotification($worker, $project, $percent)
                            );
                        }
                    }
                }
            });
    }
}

