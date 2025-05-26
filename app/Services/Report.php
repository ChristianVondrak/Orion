<?php

namespace App\Services;

use App\Models\HourlyRateUpdate;
use App\Models\UserTermination;
use App\Models\worksnapUser;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Report
{
    /**
     * Build the base Eloquent query for Activity Index between two dates.
     *
     * @param  Carbon  $start  Start datetime inclusive
     * @param  Carbon  $end    End datetime inclusive
     * @return Builder
     */
    public function activityIndexQuery(Carbon $start, Carbon $end): Builder
    {
        $startTs = $start->timestamp;
        $endTs   = $end->timestamp;

        return worksnapUser::query()
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->withAvg([
                'timmings as activity_level_avg' => function ($q) use ($startTs, $endTs) {
                    $q->whereBetween('from_timestamp', [$startTs, $endTs]);
                }
            ], 'activity_level');
    }

    /**
     * Get a paginated collection of Activity Index data.
     *
     * @param  Carbon  $start    Start datetime
     * @param  Carbon  $end      End datetime
     * @param  int     $perPage  Number of items per page
     * @return LengthAwarePaginator
     */
    public function getActivityIndexData(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        $paginator = $this->activityIndexQuery($start, $end)
            ->paginate($perPage);

        $paginator->getCollection()->transform(function ($user) {
            return (object) [
                'name'           => $user->first_name . ' ' . $user->last_name,
                'email'          => $user->email,
                'activity_index' => round(($user->activity_level_avg ?? 0) * 10, 2),
            ];
        });

        return $paginator;
    }

    /**
     * Get all Activity Index data without pagination.
     *
     * @param  Carbon  $start  Start datetime
     * @param  Carbon  $end    End datetime
     * @return Collection  Collection of stdClass {name, email, activity_index}
     */
    public function getAllActivityIndexData(Carbon $start, Carbon $end): Collection
    {
        return $this->activityIndexQuery($start, $end)
            ->get()
            ->map(fn($user) => (object)[
                'name'           => $user->first_name.' '.$user->last_name,
                'email'          => $user->email,
                'activity_index' => round(($user->activity_level_avg ?? 0) * 10, 2),
            ]);
    }

    /**
     * Build the base query for hourly‐rate updates within a date range.
     *
     * @param  Carbon  $start  Inclusive start datetime
     * @param  Carbon  $end    Inclusive end datetime
     * @return Builder
     */
    protected function hourlyRateUpdatesQuery(Carbon $start, Carbon $end): Builder
    {
        return HourlyRateUpdate::query()
            ->with('user')
            ->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Get a paginated list of hourly‐rate updates for view.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @param  int     $perPage
     * @return LengthAwarePaginator  paginator of stdClass {name, updated_at, previous_rate, new_rate}
     */
    public function getHourlyRateUpdatesData(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        $paginator = $this->hourlyRateUpdatesQuery($start, $end)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $paginator->getCollection()->transform(function(HourlyRateUpdate $u) {
            return (object)[
                'name'          => $u->user->first_name . ' ' . $u->user->last_name,
                'updated_at'    => $u->created_at->format('Y/m/d H:i'),
                'previous_rate' => $u->previous_rate,
                'new_rate'      => $u->new_rate,
            ];
        });

        return $paginator;
    }

    /**
     * Get all hourly‐rate updates for export (no pagination).
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return Collection  of stdClass {name, updated_at, previous_rate, new_rate}
     */
    public function getAllHourlyRateUpdatesData(Carbon $start, Carbon $end): Collection
    {
        return $this->hourlyRateUpdatesQuery($start, $end)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function(HourlyRateUpdate $u) {
                return (object)[
                    'name'          => $u->user->first_name . ' ' . $u->user->last_name,
                    'updated_at'    => $u->created_at->format('Y/m/d H:i'),
                    'previous_rate' => $u->previous_rate,
                    'new_rate'      => $u->new_rate,
                ];
            });
    }

    /**
     * Build the base query for users created between two dates.
     *
     * @param  Carbon  $start  Inclusive start datetime
     * @param  Carbon  $end    Inclusive end datetime
     * @return Builder
     */
    protected function usersByCreationDateQuery(Carbon $start, Carbon $end): Builder
    {
        return worksnapUser::query()
            ->with('detail')
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Get a paginated list of new users with:
     *   - name
     *   - country
     *   - position
     *   - start_date (created_at)
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @param  int     $perPage
     * @return LengthAwarePaginator
     */
    public function getNewUsersData(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        $p = $this->usersByCreationDateQuery($start, $end)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $p->getCollection()->transform(function(worksnapUser $u) {
            return (object)[
                'name'       => "{$u->first_name} {$u->last_name}",
                'country'    => $u->detail->country  ?? '',
                'position'   => $u->detail->position ?? '',
                'start_date' => $u->created_at->format('Y/m/d'),
            ];
        });

        return $p;
    }

    /**
     * Get all new users (no pagination) formatted for export.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return Collection  of stdClass {name,country,position,start_date}
     */
    public function getAllNewUsersData(Carbon $start, Carbon $end): Collection
    {
        return $this->usersByCreationDateQuery($start, $end)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function(worksnapUser $u) {
                return (object)[
                    'name'       => "{$u->first_name} {$u->last_name}",
                    'country'    => $u->detail->country  ?? '',
                    'position'   => $u->detail->position ?? '',
                    'start_date' => $u->created_at->format('Y/m/d'),
                ];
            });
    }

    /**
     * Get a paginated list of terminations including tenure.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @param  int     $perPage
     * @return LengthAwarePaginator  paginator of stdClass {
     *     name, country, department, position,
     *     start_date, termination_date, reason, tenure
     * }
     */
    public function getTerminationsData(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        $p = UserTermination::with('user.detail')
            ->whereBetween('termination_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('termination_date','desc')
            ->paginate($perPage);

        $p->getCollection()->transform(function(UserTermination $t) {
            $u       = $t->user;
            $d       = $u->detail;
            $hired   = $u->created_at;
            $ended   = Carbon::parse($t->termination_date);
            $interval= $hired->diff($ended);

            return (object)[
                'name'             => "{$u->first_name} {$u->last_name}",
                'country'          => $d->country    ?? '',
                'department'       => $d->department ?? '',
                'position'         => $d->position   ?? '',
                'start_date'       => $hired->format('Y/m/d'),
                'termination_date' => $ended->format('Y/m/d'),
                'reason'           => $t->reason,
                'tenure'           => $interval->format('%y years, %m months'),
            ];
        });

        return $p;
    }

    /**
     * Get all terminations (no pagination) for export.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return Collection  of stdClass {
     *     name, country, department, position,
     *     start_date, termination_date, reason, tenure
     * }
     */
    public function getAllTerminationsData(Carbon $start, Carbon $end): Collection
    {
        return UserTermination::with('user.detail')
            ->whereBetween('termination_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('termination_date','desc')
            ->get()
            ->map(function(UserTermination $t) {
                $u       = $t->user;
                $d       = $u->detail;
                $hired   = $u->created_at;
                $ended   = Carbon::parse($t->termination_date);
                $interval= $hired->diff($ended);

                return (object)[
                    'name'             => "{$u->first_name} {$u->last_name}",
                    'country'          => $d->country    ?? '',
                    'department'       => $d->department ?? '',
                    'position'         => $d->position   ?? '',
                    'start_date'       => $hired->format('Y/m/d'),
                    'termination_date' => $ended->format('Y/m/d'),
                    'reason'           => $t->reason,
                    'tenure'           => $interval->format('%y years, %m months'),
                ];
            });
    }

    /**
     * Build the base query for login report between two dates.
     *
     * @param  Carbon  $start  Inclusive start datetime
     * @param  Carbon  $end    Inclusive end datetime
     * @return Builder
     */
    protected function loginReportQuery(Carbon $start, Carbon $end): Builder
    {
        return worksnapUser::query()
            ->with(['timmings' => function ($query) use ($start, $end) {
                $query->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp])
                      ->orderBy('from_timestamp', 'asc');
            }])
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->whereHas('timmings', function ($query) use ($start, $end) {
                $query->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp]);
            });
    }

    /**
     * Convierte un timestamp a hora local y formato 12 horas
     */
    private function formatTimestampToLocalTime($timestamp, $format = 'Y-m-d'): string
    {
        if (!$timestamp) return '-';
        return Carbon::createFromTimestamp($timestamp)
            ->setTimezone(config('app.timezone'))
            ->format($format);
    }

    /**
     * Obtiene solo la hora en formato 12 horas de un timestamp
     */
    private function getTimeFrom($timestamp): string
    {
        if (!$timestamp) return '-';
        return Carbon::createFromTimestamp($timestamp)
            ->setTimezone(config('app.timezone'))
            ->format('h:i A');
    }

    /**
     * Verifica si una hora de inicio está retrasada
     */
    private function isDelayed(string $actualTime, string $expectedTime): bool
    {
        $actual = Carbon::createFromFormat('h:i A', $actualTime);
        $expected = Carbon::createFromFormat('h:i A', $expectedTime);
        return $actual->gt($expected);
    }

    /**
     * Calcula los minutos de retraso
     */
    private function calculateDelayMinutes(string $actualTime, string $expectedTime): int
    {
        try {
            $actual = Carbon::createFromFormat('h:i A', $actualTime);
            $expected = Carbon::createFromFormat('h:i A', $expectedTime);
            
            // Si la hora actual es menor que la esperada (ejemplo: 8:30 AM < 9:00 AM)
            if ($actual->lt($expected)) {
                return 0;
            }

            // Calculamos la diferencia en minutos
            $diffInMinutes = abs($actual->diffInMinutes($expected));
            
            return $diffInMinutes;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get a paginated list of user logins with their timing details.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @param  int     $perPage
     * @return LengthAwarePaginator
     */
    public function getLoginReportData(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        $paginator = $this->loginReportQuery($start, $end)
            ->paginate($perPage);

        $expectedStartTime = "9:00 AM";

        $paginator->getCollection()->transform(function ($user) use ($start, $end, $expectedStartTime) {
            $timings = $user->timmings;
            $delays = 0;
            $totalDelayMinutes = 0;
            
            // Agrupar timings por día
            $dailyTimings = $timings->groupBy(function ($timing) {
                return Carbon::createFromTimestamp($timing->from_timestamp)
                    ->setTimezone(config('app.timezone'))
                    ->format('Y-m-d');
            });

            foreach ($dailyTimings as $dayTimings) {
                $firstLogin = $dayTimings->first()->from_timestamp;
                $loginTime = $this->getTimeFrom($firstLogin);
                
                if ($this->isDelayed($loginTime, $expectedStartTime)) {
                    $delays++;
                    $delayMinutes = $this->calculateDelayMinutes($loginTime, $expectedStartTime);
                    $totalDelayMinutes += $delayMinutes;
                }
            }

            $firstLogin = $timings->min('from_timestamp');
            $lastLogin = $timings->max('from_timestamp');

            return (object) [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'first_login' => $this->formatTimestampToLocalTime($firstLogin),
                'first_login_time' => $this->getTimeFrom($firstLogin),
                'last_login' => $lastLogin ? $this->formatTimestampToLocalTime($lastLogin) : '-',
                'last_login_time' => $lastLogin ? $this->getTimeFrom($lastLogin) : '-',
                'average_start_time' => $this->calculateAverageStartTime($timings),
                'delays_count' => $delays,
                'total_delay_minutes' => $totalDelayMinutes,
                'total_delay_minutes_formatted' => $totalDelayMinutes . ' min',
                'is_delayed' => $delays > 0,
                'is_severely_delayed' => $totalDelayMinutes > 60
            ];
        });

        return $paginator;
    }

    /**
     * Get all login report data without pagination.
     *
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return Collection
     */
    public function getAllLoginReportData(Carbon $start, Carbon $end): Collection
    {
        $expectedStartTime = "9:00 AM";

        return $this->loginReportQuery($start, $end)
            ->get()
            ->map(function ($user) use ($start, $end, $expectedStartTime) {
                $timings = $user->timmings;
                $delays = 0;
                $totalDelayMinutes = 0;
                
                // Agrupar timings por día
                $dailyTimings = $timings->groupBy(function ($timing) {
                    return Carbon::createFromTimestamp($timing->from_timestamp)
                        ->setTimezone(config('app.timezone'))
                        ->format('Y-m-d');
                });

                foreach ($dailyTimings as $dayTimings) {
                    $firstLogin = $dayTimings->first()->from_timestamp;
                    $loginTime = $this->getTimeFrom($firstLogin);
                    
                    if ($this->isDelayed($loginTime, $expectedStartTime)) {
                        $delays++;
                        $delayMinutes = $this->calculateDelayMinutes($loginTime, $expectedStartTime);
                        $totalDelayMinutes += $delayMinutes;
                    }
                }

                $firstLogin = $timings->min('from_timestamp');
                $lastLogin = $timings->max('from_timestamp');

                return (object) [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'first_login' => $this->formatTimestampToLocalTime($firstLogin),
                    'first_login_time' => $this->getTimeFrom($firstLogin),
                    'last_login' => $lastLogin ? $this->formatTimestampToLocalTime($lastLogin) : '-',
                    'last_login_time' => $lastLogin ? $this->getTimeFrom($lastLogin) : '-',
                    'average_start_time' => $this->calculateAverageStartTime($timings),
                    'delays_count' => $delays,
                    'total_delay_minutes' => $totalDelayMinutes,
                    'total_delay_minutes_formatted' => $totalDelayMinutes . ' min',
                    'is_delayed' => $delays > 0,
                    'is_severely_delayed' => $totalDelayMinutes > 60
                ];
            });
    }

    private function calculateAverageStartTime(Collection $timings): string
    {
        if ($timings->isEmpty()) {
            return '-';
        }

        // Agrupar por día y tomar solo el primer timing de cada día
        $dailyFirstTimings = $timings->groupBy(function ($timing) {
            return Carbon::createFromTimestamp($timing->from_timestamp)
                ->setTimezone(config('app.timezone'))
                ->format('Y-m-d');
        })->map(function ($dayTimings) {
            return $dayTimings->first();
        });

        $avgMinutes = $dailyFirstTimings
            ->map(function ($timing) {
                $time = Carbon::createFromTimestamp($timing->from_timestamp)
                    ->setTimezone(config('app.timezone'));
                return $time->hour * 60 + $time->minute;
            })
            ->average();

        $hours = floor($avgMinutes / 60);
        $minutes = round($avgMinutes % 60);

        return Carbon::createFromTime($hours, $minutes)->format('h:i A');
    }
}

