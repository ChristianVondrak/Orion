<?php

namespace App\Services;

use App\Models\worksnapUser;
use App\Models\Timming;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AnnualHoursReportService
{
    /**
     * Devuelve horas anuales por profesional, agrupadas por mes.
     *
     * @param  int        $year
     * @param  int|null   $projectId
     */
    public function getAnnualHoursData(int $year, ?int $projectId = null): Collection
    {
        // Calcular timestamps una sola vez
        $start = Carbon::create($year)->startOfYear()->timestamp;
        $end   = Carbon::create($year)->endOfYear()->timestamp;

        // 1) Construir query de usuarios con al menos una timing en el periodo
        $users = WorkSnapUser::query()
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->whereHas('timmings', function ($q) use ($start, $end, $projectId) {
                $q->betweenDates($start, $end)
                  ->when($projectId, fn($q, $pid) => $q->byProject($pid));
            })
            ->with(['timmings' => function ($q) use ($start, $end, $projectId) {
                $q->betweenDates($start, $end)
                  ->when($projectId, fn($q, $pid) => $q->byProject($pid));
            }])
            ->get(['id', 'first_name', 'last_name', 'email']);

        $months = range(1, 12);

        // 2) Pivotar resultados en PHP
        return $users
            ->map(fn($user) => $this->buildUserRow($user, $months))
            ->filter()   // elimina usuarios sin horas
            ->values();
    }

    protected function buildUserRow($user, array $months): ?array
    {
        $grouped = $user->timmings
            ->groupBy(fn($t) => Carbon::createFromTimestamp($t->from_timestamp)->month);

        $row = [
            'user_id' => $user->id,
            'name'    => "{$user->first_name} {$user->last_name}",
            'email'   => $user->email,
            'months'  => [],
        ];

        $total = 0;
        foreach ($months as $m) {
            $count = $grouped->get($m, collect())->count();
            $hours = round($count * 10 / 60, 2);
            $row['months'][$m] = $hours;
            $total += $hours;
        }

        return $total > 0
            ? array_merge($row, ['total' => round($total, 2)])
            : null;
    }
}
