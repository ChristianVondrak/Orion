<?php

namespace App\Services;

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
}

