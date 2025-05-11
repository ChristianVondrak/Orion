<?php

namespace App\Services;

use App\Models\HourlyRateUpdate;
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
}

