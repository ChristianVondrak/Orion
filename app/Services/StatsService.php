<?php
namespace App\Services;

use App\Models\Project;
use App\Models\Timming;
use App\Models\worksnapUser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatsService
{
    /**
     * Calcula la ocupación global del mes actual.
     *
     * @param  Carbon $start     Primer día del mes
     * @param  Carbon $end       Último día del mes
     * @param  int    $hoursPerDay  Horas estándar por día (p.ej. 8)
     * @return array
     */
    public function occupancyRate(Carbon $start, Carbon $end, int $hoursPerDay = 8): array
    {
        // 1) Días laborales en el mes completo
        $periodMonth = CarbonPeriod::create($start, '1 day', $end);
        $workingDaysInMonth = collect($periodMonth)
            ->filter->isWeekday()
            ->count();

        // 2) Días laborales transcurridos hasta hoy
        $today = min(Carbon::now(), $end);
        $periodToDate = CarbonPeriod::create($start, '1 day', $today);
        $workingDaysToDate = collect($periodToDate)
            ->filter->isWeekday()
            ->count();

        // 3) Usuarios activos (con email y pago por hora)
        $userCount = worksnapUser::query()
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->whereHas('projectUsers', function ($q) {
                $q->where('payment_type', 'hourly');
            })
            ->count();

        // 4) Horas esperadas hasta la fecha
        $expectedHours = $userCount * $hoursPerDay * $workingDaysToDate;

        // 5) Horas reales trabajadas (bloques de 10 min)
        $seconds = Timming::whereBetween('from_timestamp', [
                $start->timestamp,
                $end->timestamp
            ])->count() * 10 * 60;
        $actualHours = round($seconds / 3600, 2);

        // 6) Tasa de ocupación %
        $occupancyRate = $expectedHours > 0
            ? round(($actualHours / $expectedHours) * 100, 2)
            : 0;

        return [
            'user_count'             => $userCount,
            'working_days_in_month'  => $workingDaysInMonth,
            'working_days_to_date'   => $workingDaysToDate,
            'expected_hours_to_date' => round($expectedHours, 2),
            'actual_hours'           => $actualHours,
            'occupancy_rate'         => $occupancyRate,
            'status'                 => $this->getOccupancyStatus($occupancyRate),
        ];
    }

    /**
     * Status según la tasa de ocupación.
     *
     * @param float $rate
     * @return string  optimal|moderate|low
     */
    private function getOccupancyStatus(float $rate): string
    {
        if ($rate >= 90) return 'optimal';
        if ($rate >= 70) return 'moderate';
        return 'low';
    }

    /**
     * Lista por proyecto % de horas completadas en el mes.
     *
     * @param  Carbon $start
     * @param  Carbon $end
     * @param  int    $hoursPerDay
     * @return Collection
     */
    public function projectHourCompletion(
        Carbon $start,
        Carbon $end,
        int $hoursPerDay = 8
    ): Collection {
        // días laborales del mes completo
        $periodMonth = CarbonPeriod::create($start, '1 day', $end);
        $workingDays = collect($periodMonth)
            ->filter->isWeekday()
            ->count();

        // días laborales transcurridos hasta hoy
        $today = min(Carbon::now(), $end);
        $periodToDate = CarbonPeriod::create($start, '1 day', $today);
        $workingDaysToDate = collect($periodToDate)
            ->filter->isWeekday()
            ->count();

        return Project::whereHas('projectUsers', function($q) {
                $q->where('payment_type', 'hourly');
            })
            ->get()
            ->map(function ($project) use ($start, $end, $hoursPerDay, $workingDaysToDate) {
                // Solo usuarios con pago por hora
                $userCount = $project->projectUsers()
                    ->where('payment_type', 'hourly')
                    ->count();

                // Horas planificadas hasta la fecha: usuarios × horas/día × días hábiles transcurridos
                $planned = $userCount * $hoursPerDay * $workingDaysToDate;

                // Horas reales
                $seconds = $project->timmings()
                        ->whereBetween('from_timestamp', [
                            $start->timestamp,
                            $end->timestamp
                        ])->count() * 10 * 60;
                $actual = round($seconds / 3600, 2);

                $percentage = $planned > 0
                    ? round(min(100, ($actual / $planned) * 100), 2)
                    : 0;

                return [
                    'project_id'    => $project->id,
                    'project_name'  => $project->name,
                    'planned_hours' => $planned,
                    'actual_hours'  => $actual,
                    'percentage'    => $percentage,
                    'status'        => $this->getProjectStatus($percentage),
                ];
            });
    }

    /**
     * Estado según % completado.
     *
     * @param float $percentage
     * @return string  on-track|warning|over|behind
     */
    private function getProjectStatus(float $percentage): string
    {
        if ($percentage >= 90 && $percentage <= 110) return 'on-track';
        if ($percentage >= 70 && $percentage < 90)  return 'warning';
        if ($percentage > 110)                     return 'over';
        return 'behind';
    }
}
